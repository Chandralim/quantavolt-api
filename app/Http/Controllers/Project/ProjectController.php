<?php

namespace App\Http\Controllers\Project;

use App\Exceptions\MyException;
use App\Helpers\MyLib;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Model\Project;
use App\Model\ProjectMaterial;
use App\Model\ProjectWorkingTool;
use App\ProjectAdditional;
use App\ProjectWorker;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class ProjectController extends Controller
{
    private $auth;
    public function __construct()
    {
        $this->auth = MyLib::user();
    }

    public function index(Request $request)
    {
        MyLib::checkScope($this->auth, ['ap-project-view']);

        //======================================================================================================
        // Pembatasan Data hanya memerlukan limit dan offset
        //======================================================================================================

        $limit = 250; // Limit +> Much Data
        if (isset($request->limit)) {
            if ($request->limit <= 250) {
                $limit = $request->limit;
            } else {
                throw new MyException(['message' => 'Max Limit 250']);
            }
        }

        $offset = isset($request->offset) ? (int) $request->offset : 0; // example offset 400 start from 401

        //======================================================================================================
        // Jika Halaman Ditentutkan maka $offset akan disesuaikan
        //======================================================================================================
        if (isset($request->page)) {
            $page = (int) $request->page;
            $offset = $page * $limit - $limit;
        }

        //======================================================================================================
        // Init Model
        //======================================================================================================
        $model_query = Project::offset($offset)->limit($limit);

        //======================================================================================================
        // Model Sorting | Example $request->sort = "quotation_itemname:desc,role:desc";
        //======================================================================================================

        if ($request->sort) {
            $sort_lists = [];

            $sorts = explode(',', $request->sort);
            foreach ($sorts as $key => $sort) {
                $side = explode(':', $sort);
                $side[1] = isset($side[1]) ? $side[1] : 'ASC';
                $sort_lists[$side[0]] = $side[1];
            }

            if (isset($sort_lists['no'])) {
                $model_query = $model_query->orderBy('no', $sort_lists['no']);
            }

            if (isset($sort_lists['title'])) {
                $model_query = $model_query->orderBy('title', $sort_lists['title']);
            }

            if (isset($sort_lists['date_start'])) {
                $model_query = $model_query->orderBy('date_start', $sort_lists['date_start']);
            }

            if (isset($sort_lists['finish_start'])) {
                $model_query = $model_query->orderBy('finish_start', $sort_lists['finish_start']);
            }

            // if (isset($sort_lists["role"])) {
            //   $model_query = $model_query->orderBy(function($q){
            //     $q->from("internal.roles")
            //     ->select("name")
            //     ->whereColumn("id","auths.role_id");
            //   },$sort_lists["role"]);
            // }

            // if (isset($sort_lists["auth"])) {
            //   $model_query = $model_query->orderBy(function($q){
            //     $q->from("quotation_items as u")
            //     ->select("u.quotation_itemname")
            //     ->whereColumn("u.id","quotation_items.id");
            //   },$sort_lists["auth"]);
            // }
        } else {
            $model_query = $model_query->orderBy('no', 'ASC');
        }
        //======================================================================================================
        // Model Filter | Example $request->like = "quotation_itemname:%quotation_itemname,role:%role%,name:role%,";
        //======================================================================================================

        // if ($request->like) {
        //   $like_lists=[];

        //   $likes=explode(",",$request->like);
        //   foreach ($likes as $key => $like) {
        //     $side = explode(":",$like);
        //     $side[1]=isset($side[1])?$side[1]:'';
        //     $like_lists[$side[0]]=$side[1];
        //   }

        //   if (isset($like_lists["quotation_itemname"])) {
        //     $model_query = $model_query->orWhere("quotation_itemname","ilike",$like_lists["quotation_itemname"]);
        //   }

        //   // if (isset($like_lists["role"])) {
        //   //   $model_query = $model_query->orWhere("role","ilike",$like_lists["role"]);
        //   // }
        // }

        // ==============
        // Model Filter
        // ==============

        if (isset($request->no)) {
            $model_query = $model_query->where('no', 'ilike', '%' . $request->no . '%');
        }

        if (isset($request->title)) {
            $model_query = $model_query->where('title', 'ilike', '%' . $request->title . '%');
        }

        $model_query = $model_query->with('creator', 'updator', 'customer')->get();

        return response()->json(
            [
                // "data"=>QuotationItemResource::collection($quotation_items->keyBy->id),
                'data' => ProjectResource::collection($model_query),
            ],
            200,
        );
    }

    public function show(ProjectRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-project-view']);

        $model_query = Project::where('no', $request->no)
            ->with([
                'creator',
                'updator',
                'customer',
                'project_materials' => function ($q) {
                    $q->with([
                        'unit',
                        'item' => function ($q2) {
                            $q2->with('unit');
                        },
                        'creator',
                        'updator',
                    ]);
                },
                'project_workers' => function ($q) {
                    $q->with([
                        'employee',
                        'creator',
                        'updator',
                    ]);
                },
                'project_working_tools' => function ($q) {
                    $q->with([
                        'unit',
                        'item' => function ($q2) {
                            $q2->with('unit');
                        },
                        'creator',
                        'updator',
                    ]);
                },
                'project_additionals' => function ($q) {
                    $q->with([
                        'unit',
                        'creator',
                        'updator',
                    ]);
                },
            ])
            ->first();
        return response()->json(
            [
                'data' => new ProjectResource($model_query),
            ],
            200,
        );
    }

    public function store(ProjectRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-project-add']);

        DB::beginTransaction();
        try {
            $sql = Project::orderBy('no', 'desc')->first();
            if ($sql) {
                $no = MyLib::nextNo($sql->no);
            } else {
                $no = MyLib::formatNo('PRO');
            }

            $model_query = new Project();
            $model_query->no = $no;
            $model_query->title = $request->title;
            $model_query->date = date('Y-m-d');
            $model_query->location = $request->location;
            $model_query->customer_code = $request->customer_code;
            $model_query->type = $request->type;
            $model_query->date_start = $request->date_start;
            $model_query->date_finish = $request->date_finish;
            $model_query->status = $request->status;
            $model_query->note = $request->note;
            $model_query->created_at = date('Y-m-d H:i:s');
            $model_query->created_by = $this->auth->id;
            $model_query->updated_at = date('Y-m-d H:i:s');
            $model_query->updated_by = $this->auth->id;
            $model_query->save();

            DB::commit();
            return response()->json(
                [
                    'message' => 'Proses tambah project berhasil',
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollback();
            if ($e->getCode() == 1) {
                return response()->json(
                    [
                        'message' => $e->getMessage(),
                    ],
                    400,
                );
            }

            return response()->json(
                [
                    'message' => 'Proses tambah project gagal',
                ],
                400,
            );
        }
    }

    public function update(ProjectRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-project-edit']);

        DB::beginTransaction();
        try {
            $model_query = Project::where('no', $request->no)->first();
            $model_query->no = trim($request->no);
            $model_query->title = $request->title;
            $model_query->location = $request->location;
            $model_query->customer_code = $request->customer_code;
            $model_query->type = $request->type;
            $model_query->date_start = $request->date_start;
            $model_query->date_finish = $request->date_finish;
            $model_query->status = $request->status;
            $model_query->note = $request->note;

            $model_query->updated_at = date('Y-m-d H:i:s');
            $model_query->updated_by = $this->auth->id;
            $model_query->save();

            DB::commit();
            return response()->json(
                [
                    'message' => 'Proses ubah project berhasil',
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollback();
            if ($e->getCode() == 1) {
                return response()->json(
                    [
                        'message' => $e->getMessage(),
                    ],
                    400,
                );
            }

            return response()->json(
                [
                    'message' => 'Proses ubah project gagal',
                ],
                400,
            );
        }
    }

    public function delete(ProjectRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-project-remove']);

        DB::beginTransaction();

        try {
            $model_query = Project::find($request->no);
            if (!$model_query) {
                throw new \Exception('Data tidak terdaftar', 1);
            }
            $model_query->delete();

            DB::commit();
            return response()->json(
                [
                    'message' => 'Proses delete data berhasil',
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollback();
            if ($e->getCode() == '23503') {
                return response()->json(
                    [
                        'message' => 'Data tidak dapat dihapus, data masih terkait dengan data yang lain nya',
                    ],
                    400,
                );
            }

            if ($e->getCode() == 1) {
                return response()->json(
                    [
                        'message' => $e->getMessage(),
                    ],
                    400,
                );
            }

            return response()->json(
                [
                    'message' => 'Proses hapus data gagal',
                ],
                400,
            );
        }
    }

    // public function material(Request $request)
    // {
    //     $scopes = $this->auth->listPermissions();
    //     $has_value = count(array_intersect(['ap-project_material-edit'], $scopes));
    //     if($has_value){
    //         throw new MyException(["message"=>"Forbidden"],403);
    //     }

    //     // MyLib::checkScope($this->auth, ['ap-project_material-edit']);

    //     $project_materials_in = json_decode($request->project_materials, true);

    //     $rules = [
    //         'project_materials'                     => 'required|array',
    //         'project_materials.*.item_name'         => 'required',
    //         'project_materials.*.qty_assumption'    => 'required|numeric',
    //         'project_materials.*.stock'             => 'nullable|numeric',                                    
    //         'project_materials.*.price_assumption'  => 'nullable|numeric',
    //         'project_materials.*.qty_realization'   => 'nullable|numeric',
    //         'project_materials.*.price_realization' => 'nullable|numeric',
    //         'project_materials.*.item'              => 'nullable|array',
    //         'project_materials.*.item.code'         => 'nullable|exists:\App\Model\Item,code',
    //         'project_materials.*.unit'              => 'required|array',
    //         'project_materials.*.unit.code'         => 'required|exists:\App\Model\Unit,code',
    //         'project_materials.*.status'            => 'required|in:Add,Edit,Remove',
    //     ];

    //     $messages = [
    //         'project_materials.required' => 'Material Project harus diisi',
    //         'project_materials.array' => 'Format Project Material Salah',
    //     ];

    //     foreach ($project_materials_in as $index => $msg) {
    //         $messages["project_materials.{$index}.item_name.required"]          = 'Baris #' . ($index + 1) . '. Nama item tidak boleh kosong.';

    //         $messages["project_materials.{$index}.qty_assumption.required"]     = 'Baris #' . ($index + 1) . '. Jumlah tidak boleh kosong.';
    //         $messages["project_materials.{$index}.qty_assumption.numeric"]      = 'Baris #' . ($index + 1) . '. Jumlah harus berupa angka.';

    //         // $messages["project_materials.{$index}.stock.required"]              = 'Baris #' . ($index + 1) . '. Stok tidak boleh kosong.';
    //         $messages["project_materials.{$index}.stock.numeric"]               = 'Baris #' . ($index + 1) . '. Stok harus berupa angka.';

    //         // $messages["project_materials.{$index}.qty_realization.required"]    = 'Baris #' . ($index + 1) . '. Jumlah Realisasi item tidak boleh kosong.';
    //         $messages["project_materials.{$index}.qty_realization.numeric"]     = 'Baris #' . ($index + 1) . '. Jumlah Realisasi harus berupa angka.';

    //         // $messages["project_materials.{$index}.price_assumption.required"]   = 'Baris #' . ($index + 1) . '. Asumsi Harga tidak boleh kosong.';
    //         $messages["project_materials.{$index}.price_assumption.numeric"]    = 'Baris #' . ($index + 1) . '. Asumsi Harga harus berupa angka.';

    //         // $messages["project_materials.{$index}.price_realization.required"]  = 'Baris #' . ($index + 1) . '. Harga Realisasi tidak boleh kosong.';
    //         $messages["project_materials.{$index}.price_realization.numeric"]   = 'Baris #' . ($index + 1) . '. Harga Realisasi harus berupa angka.';

    //         // $messages["project_materials.{$index}.item_code.required"]          = 'Baris #' . ($index + 1) . '. Item di project material harus di isi';
    //         $messages["project_materials.{$index}.item.array"]                  = 'Baris #' . ($index + 1) . '. Format Item di project material Salah';
    //         $messages["project_materials.{$index}.item.code.required"]          = "Baris #" . ($index + 1) . " . Item harus di isi";
    //         $messages["project_materials.{$index}.item.code.exists"]            = 'Baris #' . ($index + 1) . '. Item tidak terdaftar';

    //         $messages["project_materials.{$index}.unit.required"]               = 'Baris #' . ($index + 1) . '. Satuan di project material harus di isi';
    //         $messages["project_materials.{$index}.unit.array"]                  = 'Baris #' . ($index + 1) . '. Format Satuan di project material Salah';
    //         $messages["project_materials.{$index}.unit.code.required"]          = 'Baris #' . ($index + 1) . '. Satuan harus di isi';
    //         $messages["project_materials.{$index}.unit.code.exists"]            = 'Baris #' . ($index + 1) . '. Satuan tidak terdaftar';
    //     }

    //     $validator = \Validator::make(['project_materials' => $project_materials_in], $rules, $messages);

    //     if ($validator->fails()) {
    //         foreach ($validator->messages()->all() as $k => $v) {
    //             throw new MyException(['message' => $v], 400);
    //         }
    //     }

    //     $new_datas=[];
    //     $prepare_insert_index=[];
    //     DB::beginTransaction();
    //     try {
    //         $sql = Project::orderBy('no', 'desc')->first();
    //         if ($sql) {
    //             $no = $sql->no;
    //         } else {
    //             throw new Exception('Nomor Project Tidak Terdaftar');
    //         }

    //         $model_query = Project::where('no', $request->no)->first();

            
    //         $check_unique_data_in = array_map(function ($value) {
    //             $item_code = $value['item'] && $value['item']['code']!="" ? $value['item']['code'] : "";
    //             return strtolower($item_code.$value['unit']['code'].$value['item_name']);
    //         },$project_materials_in);

    //         $auth_id = $this->auth->id;

    //         $old_datas = ProjectMaterial::where('project_no', $model_query->no)
    //         ->get()->toArray();

    //         foreach ($old_datas as $key => $value) {
    //             $index = array_search(strtolower($value["item_code"].$value["unit_code"].$value["item_name"]),$check_unique_data_in);
                
    //             if($index!==false){
                    
    //                 $count_change_data=0;
    //                 if($value["qty_assumption"]!=$project_materials_in[$index]['qty_assumption'] && !$value["is_locked"] && $value["created_by"]==$auth_id){
    //                     $has_value = count(array_intersect(['dp-project_material-manage-qty_assumption'], $scopes));
    //                     if($has_value){
    //                         $value["qty_assumption"]=MyLib::emptyStrToNull($project_materials_in[$index]['qty_assumption']);
    //                         $count_change_data++;
    //                     }else {
    //                         throw new Exception('Tidak ada izin mengelola jumlah asumsi');
    //                     }
    //                 }

    //                 if($value["qty_realization"]!=$project_materials_in[$index]['qty_realization']){
    //                     $has_value = count(array_intersect(['dp-project_material-manage-qty_realization'], $scopes));
    //                     if ($has_value) {
    //                         $value["qty_realization"]=MyLib::emptyStrToNull($project_materials_in[$index]['qty_realization']);
    //                         $count_change_data++;
    //                     }else {
    //                         throw new Exception('Tidak ada izin mengelola jumlah realisasi');
    //                     }
    //                 }

    //                 if($value["stock"]!=$project_materials_in[$index]['stock'] && !$value["is_locked"]){
    //                     $has_value = count(array_intersect(['dp-project_material-manage-stock'], $scopes));
    //                     if($has_value){
    //                         $value["stock"]=MyLib::emptyStrToNull($project_materials_in[$index]['stock']);
    //                         $count_change_data++;
    //                     }else {
    //                         throw new Exception('Tidak ada izin mengelola stock');
    //                     }
    //                 }

    //                 if($value["price_assumption"]!=$project_materials_in[$index]['price_assumption'] && !$value["is_locked"]){
    //                     $has_value = count(array_intersect(['dp-project_material-manage-price_assumption'], $scopes));
    //                     if($has_value){
    //                         $value["price_assumption"]=MyLib::emptyStrToNull($project_materials_in[$index]['price_assumption']);
    //                         $count_change_data++;
    //                     }else {
    //                         throw new Exception('Tidak ada izin mengelola asumsi harga');
    //                     }
    //                 }

    //                 if($value["price_realization"]!=$project_materials_in[$index]['price_realization']){
    //                     $has_value = count(array_intersect(['dp-project_material-manage-price_realization'], $scopes));
    //                     if($has_value){
    //                         $value["price_realization"]=MyLib::emptyStrToNull($project_materials_in[$index]['price_realization']);
    //                         $count_change_data++;
    //                     }else {
    //                         throw new Exception('Tidak ada izin mengelola asumsi harga');
    //                     }
    //                 }

    //                 if($value["is_locked"]!=$project_materials_in[$index]['is_locked']){
    //                     $has_value = count(array_intersect(['dp-project_material-manage-is_locked'], $scopes));
    //                     if($has_value){
    //                         $value["is_locked"]=MyLib::emptyStrToNull($project_materials_in[$index]['is_locked']);
    //                         $count_change_data++;
    //                     }else {
    //                         throw new Exception('Tidak ada izin mengelola kunci data');
    //                     }
    //                 }

    //                 $value["note"] = $project_materials_in[$index]['note'] == "" ? null : $project_materials_in[$index]['note'];
    //                 $project_materials_in[$index]["had_old_data"]=true;


    //                 $has_value = count(array_intersect(['ap-project_material_item-edit'], $scopes));
    //                 if(!$has_value && $count_change_data > 0){
    //                     throw new Exception('Ubah Project Material Item Tidak diizinkan');
    //                 }

    //             }else{
    //                 $has_value = count(array_intersect(['ap-project_material_item-remove'], $scopes));
    //                 if($has_value && !$value["is_locked"] && $value["created_by"]==$auth_id){
    //                     continue;
    //                 }
    //             }

    //             array_push($new_datas,$value);
    //         }
            
            

    //         foreach ($project_materials_in as $key => $value) {

    //             if(isset($value["had_old_data"])){continue;}

    //             if(!count(array_intersect(['ap-project_material_item-add'], $scopes))  )
    //             throw new Exception('Tambah Project Material Item Tidak diizinkan');

    //             if(!count(array_intersect(['dp-project_material-manage-item_code'], $scopes))  && $item_code!="")
    //             throw new Exception('Tidak ada izin mengelola Kode item');

    //             if(!count(array_intersect(['dp-project_material-manage-unit_code'], $scopes))  && $value['unit']['code']!="")
    //             throw new Exception('Tidak ada izin mengelola Kode unit');
                
    //             if(!count(array_intersect(['dp-project_material-manage-item_name'], $scopes)) && $value['item_name']!="")
    //             throw new Exception('Tidak ada izin mengelola Nama item');

    //             if(!count(array_intersect(['dp-project_material-manage-qty_assumption'], $scopes)) && $value['qty_assumption']!="")
    //             throw new Exception('Tidak ada izin mengelola Asumsi jumlah');
                
    //             if(!count(array_intersect(['dp-project_material-manage-qty_realization'], $scopes)) && $value['qty_realization']!="")
    //             throw new Exception('Tidak ada izin mengelola Realisasi jumlah');

    //             if(!count(array_intersect(['dp-project_material-manage-stock'], $scopes)) && $value['stock']!="")
    //             throw new Exception('Tidak ada izin mengelola Stock');

    //             if(!count(array_intersect(['dp-project_material-manage-price_assumption'], $scopes)) && $value['price_assumption']!="")
    //             throw new Exception('Tidak ada izin mengelola Asumsi Harga');

    //             if(!count(array_intersect(['dp-project_material-manage-price_realization'], $scopes)) && $value['price_realization']!="")
    //             throw new Exception('Tidak ada izin mengelola Realisasi Harga');

    //             if(!count(array_intersect(['dp-project_material-manage-is_locked'], $scopes)) && $value['is_locked'])
    //             throw new Exception('Tidak ada izin mengelola Kunci');

    //             $item_code = $value['item'] ? $value['item']['code'] : "";
                
    //             $data =[
    //                 'project_no'        =>$model_query->no,
    //                 'item_code'         =>$item_code == "" ? null : $item_code,
    //                 'unit_code'         =>$value['unit']['code'],
    //                 'item_name'         =>$value['item_name'],
    //                 'qty_assumption'    =>$value['qty_assumption'],
    //                 'qty_realization'   =>$value['qty_realization']== "" ? null : $value['qty_realization'],
    //                 'stock'             =>$value['stock']== "" ? null : $value['stock'],
    //                 'price_assumption'  =>$value['price_assumption']== "" ? null : $value['price_assumption'],
    //                 'price_realization' =>$value['price_realization']== "" ? null : $value['price_realization'],
    //                 'is_locked'         =>$value['is_locked'],
    //                 'note'              =>$value['note']== "" ? null : $value['note'],
    //                 'created_at'        =>date('Y-m-d H:i:s'),
    //                 'created_by'        =>$auth_id,
    //                 'updated_at'        =>date('Y-m-d H:i:s'),
    //                 'updated_by'        =>$auth_id,
    //             ];

    //             if(count($prepare_insert_index) > 0){
    //                 array_splice($new_datas,$prepare_insert_index[0],0,$data);
    //                 array_splice($prepare_insert_index,0,1);
    //             }else {
    //                 array_push($new_datas,$data);
    //             }
    //         }
            
    //         ProjectMaterial::where('project_no', $model_query->no)->delete();

    //         $code_items = [];
    //         foreach ($new_datas as $key => $value) {
    //             $ordinal = $key + 1;
                
    //             if(in_array(strtolower($value['item_code'] .$value['unit_code']. $value['item_name']),$code_items) == 1){
    //                 throw new \Exception("Maaf terdapat Nama Item yang sama");            
    //             }
                
    //             array_push($code_items, strtolower($value['item_code'] .$value['unit_code']. $value['item_name']));

    //             // $price_realization = 0;
    //             // $item_get = Item::where('code', $item_code)->first();
    //             // if($item_get){
    //             //   $price_realization = $item_get->price_realization;
    //             // }
    //             $project_material                       = new ProjectMaterial();
    //             $project_material->project_no           = $value["project_no"];
    //             $project_material->ordinal              = $ordinal;

    //             $project_material->item_code            = $value["item_code"] ;
    //             $project_material->unit_code            = $value['unit_code'];
    //             $project_material->item_name            = $value['item_name'];
    //             $project_material->qty_assumption       = $value['qty_assumption'];
    //             $project_material->qty_realization      = $value['qty_realization'];
    //             $project_material->stock                = $value['stock'];
    //             $project_material->price_assumption     = $value['price_assumption'];
    //             $project_material->price_realization    = $value['price_realization'];
    //             $project_material->is_locked            = $value['is_locked'];
    //             $project_material->note                 = $value['note'];
    //             $project_material->created_at           = $value['created_at'];
    //             $project_material->created_by           = $value['created_by'];
    //             $project_material->updated_at           = $value['updated_at'];
    //             $project_material->updated_by           = $value['updated_by'];
    //             $project_material->save();
    //         }
    //         DB::commit();
    //         return response()->json(
    //             [
    //                 'message' => 'Proses tambah data berhasil',
    //             ],
    //             200,
    //         );
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // throw new MyException(['message' => $e->getMessage()], 400);
    //         throw new MyException(['message' => $e->getLine() ."-".$e->getMessage() ], 400);
    //     }
    // }

    public function material(Request $request)
    {
        $scopes = $this->auth->listPermissions();

        $has_value = count(array_intersect(['ap-project_material-edit'], $scopes));
        if($has_value){
            throw new MyException(["message"=>"Forbidden"],403);
        }

        // MyLib::checkScope($this->auth, ['ap-project_material-edit']);

        $project_materials_in = json_decode($request->project_materials, true);

        $rules = [
            'project_materials'                     => 'required|array',
            'project_materials.*.item_name'         => 'required',
            'project_materials.*.qty_assumption'    => 'required|numeric',
            'project_materials.*.stock'             => 'nullable|numeric',                                    
            'project_materials.*.price_assumption'  => 'nullable|numeric',
            'project_materials.*.qty_realization'   => 'nullable|numeric',
            'project_materials.*.price_realization' => 'nullable|numeric',
            'project_materials.*.item'              => 'nullable|array',
            'project_materials.*.item.code'         => 'nullable|exists:\App\Model\Item,code',
            'project_materials.*.unit'              => 'required|array',
            'project_materials.*.unit.code'         => 'required|exists:\App\Model\Unit,code',
            'project_materials.*.status'            => 'required|in:Add,Edit,Remove',
        ];

        $messages = [
            'project_materials.required' => 'Material Project harus diisi',
            'project_materials.array' => 'Format Project Material Salah',
        ];

        foreach ($project_materials_in as $index => $msg) {
            $messages["project_materials.{$index}.item_name.required"]          = 'Baris #' . ($index + 1) . '. Nama item tidak boleh kosong.';

            $messages["project_materials.{$index}.qty_assumption.required"]     = 'Baris #' . ($index + 1) . '. Jumlah tidak boleh kosong.';
            $messages["project_materials.{$index}.qty_assumption.numeric"]      = 'Baris #' . ($index + 1) . '. Jumlah harus berupa angka.';

            // $messages["project_materials.{$index}.stock.required"]              = 'Baris #' . ($index + 1) . '. Stok tidak boleh kosong.';
            $messages["project_materials.{$index}.stock.numeric"]               = 'Baris #' . ($index + 1) . '. Stok harus berupa angka.';

            // $messages["project_materials.{$index}.qty_realization.required"]    = 'Baris #' . ($index + 1) . '. Jumlah Realisasi item tidak boleh kosong.';
            $messages["project_materials.{$index}.qty_realization.numeric"]     = 'Baris #' . ($index + 1) . '. Jumlah Realisasi harus berupa angka.';

            // $messages["project_materials.{$index}.price_assumption.required"]   = 'Baris #' . ($index + 1) . '. Asumsi Harga tidak boleh kosong.';
            $messages["project_materials.{$index}.price_assumption.numeric"]    = 'Baris #' . ($index + 1) . '. Asumsi Harga harus berupa angka.';
            
            // $messages["project_materials.{$index}.price_realization.required"]  = 'Baris #' . ($index + 1) . '. Harga Realisasi tidak boleh kosong.';
            $messages["project_materials.{$index}.price_realization.numeric"]   = 'Baris #' . ($index + 1) . '. Harga Realisasi harus berupa angka.';

            // $messages["project_materials.{$index}.item_code.required"]          = 'Baris #' . ($index + 1) . '. Item di project material harus di isi';
            $messages["project_materials.{$index}.item.array"]                  = 'Baris #' . ($index + 1) . '. Format Item di project material Salah';
            $messages["project_materials.{$index}.item.code.required"]          = "Baris #" . ($index + 1). " . Item harus di isi";
            $messages["project_materials.{$index}.item.code.exists"]            = 'Baris #' . ($index + 1) . '. Item tidak terdaftar';

            $messages["project_materials.{$index}.unit.required"]               = 'Baris #' . ($index + 1) . '. Satuan di project material harus di isi';
            $messages["project_materials.{$index}.unit.array"]                  = 'Baris #' . ($index + 1) . '. Format Satuan di project material Salah';
            $messages["project_materials.{$index}.unit.code.required"]          = 'Baris #' . ($index + 1) . '. Satuan harus di isi';
            $messages["project_materials.{$index}.unit.code.exists"]            = 'Baris #' . ($index + 1) . '. Satuan tidak terdaftar';
        }
        
        $validator = \Validator::make(['project_materials' => $project_materials_in], $rules, $messages);

        if ($validator->fails()) {
            foreach ($validator->messages()->all() as $k => $v) {
                throw new MyException(['message' => $v], 400);
            }
        }
        

        $new_datas=[];
        $prepare_insert_index=[];
        DB::beginTransaction();
        try {
            $model_query = Project::where('no', $request->no)->first();
            if (!$model_query) {
                throw new Exception('Nomor Project Tidak Terdaftar');
            }

            $auth_id = $this->auth->id;

            $data_from_db = ProjectMaterial::where('project_no', $model_query->no)
            ->orderBy("ordinal","asc")
            ->get()->toArray();
            
            $code_items=[];


            $in_keys = array_filter($project_materials_in,function ($x){
                return isset($x["key"]);                
            });

            $in_keys = array_map(function ($x){
                return $x["key"];                
            },$in_keys);

            $am_ordinal_db=array_map(function ($x){
                return $x["ordinal"];                
            },$data_from_db);

            if(count(array_diff($in_keys, $am_ordinal_db)) > 0 || count(array_diff($am_ordinal_db,$in_keys)) > 0 ){
                throw new Exception('Ada ketidak sesuaian data, harap hubungi staff IT atau refresh browser anda');
            }

            $ordinal=1;
            $for_deletes = [];
            $for_edits = [];
            $for_adds = [];
            $data_to_processes=[];
            foreach ($project_materials_in as $k => $v) {
                $item_code = $v['item'] ? $v['item']['code'] : "";

                if($item_code && $item_code!="000")
                    $unit_code = \App\Model\Item::where("code",$item_code)->first()->unit_code;
                else
                    $unit_code = $v['unit'] ? $v['unit']['code'] : "";

                if(in_array($v["status"],["Add","Edit"])){
                    if(in_array(strtolower($item_code.$unit_code.$v['item_name']),$code_items) == 1){
                        throw new \Exception("Maaf terdapat Nama Item yang sama");            
                    }                    
                    array_push($code_items, strtolower($item_code.$unit_code.$v['item_name']));
                }

                $project_materials_in[$k]["item_code"] = $item_code;
                $project_materials_in[$k]["unit_code"] = $unit_code;

                if($v["status"]!=="Remove"){
                    $project_materials_in[$k]["ordinal"] = $ordinal;
                    $ordinal++;
                    if($v["status"]=="Edit")
                        array_push($for_edits,$project_materials_in[$k]);
                    elseif ($v["status"]=="Add")
                        array_push($for_adds,$project_materials_in[$k]);
                }else
                    array_push($for_deletes,$project_materials_in[$k]);
            }

            $data_to_processes = array_merge($for_deletes,$for_edits,$for_adds);

            $ordinal = 1;
            foreach ($data_to_processes as $k => $v) {
                $index = false;

                if(isset($v["key"])){
                    // \App\Helpers\MyLog::logging($v,"check");
                    // throw new Exception('????');
                    $index = array_search($v["key"],$am_ordinal_db);
                }

    
                $v["item_code"] = MyLib::emptyStrToNull($v["item_code"]);
                $v["note"] = MyLib::emptyStrToNull($v["note"]);
                $v["qty_assumption"]=MyLib::emptyStrToNull($v["qty_assumption"]);
                $v["qty_realization"]=MyLib::emptyStrToNull($v["qty_realization"]);
                $v["stock"]=MyLib::emptyStrToNull($v["stock"]);
                $v["price_assumption"]=MyLib::emptyStrToNull($v["price_assumption"]);
                $v["price_realization"]=MyLib::emptyStrToNull($v["price_realization"]);

                if($v["status"]=="Remove"){

                    if($index===false){
                        throw new \Exception("Data yang ingin dihapus tidak ditemukan");            
                    }else {
                        $dt = $data_from_db[ $index ];
                        $has_permit = count(array_intersect(['ap-project_material_item-remove'], $scopes));
                        if(!$dt["is_locked"] && $dt["created_by"]==$auth_id && $has_permit){
                            ProjectMaterial::where("project_no",$model_query->no)->where("ordinal",$dt["ordinal"])->delete();
                        }
                    }

                }else if($v["status"]=="Edit"){

                    if($index===false){
                        throw new \Exception("Data yang ingin diubah tidak ditemukan".$k);            
                    }else {
                        $dt = $data_from_db[ $index ];
                        $has_permit = count(array_intersect(['ap-project_material_item-edit'], $scopes));
                        if(!$has_permit){
                            throw new Exception('Ubah Project Material Item Tidak diizinkan');
                        }
                        
                        if($v["qty_assumption"]!=$dt['qty_assumption']){
                            $has_value = count(array_intersect(['dp-project_material-manage-qty_assumption'], $scopes));

                            if($dt["is_locked"] || !$has_value || $dt["created_by"]!=$auth_id)
                            throw new Exception('Ubah Jumlah Asumsi Tidak diizinkan'); 
                        }
    
                        if($v["qty_realization"]!=$dt['qty_realization']){                            
                            $has_value = count(array_intersect(['dp-project_material-manage-qty_realization'], $scopes));
                            if($dt["is_locked"] || !$has_value)
                            throw new Exception('Ubah Jumlah Realisasi Tidak diizinkan'); 
                        }

                        if($v["stock"]!=$dt['stock']){                            
                            $has_value = count(array_intersect(['dp-project_material-manage-stock'], $scopes));
                            if($dt["is_locked"] || !$has_value)
                            throw new Exception('Ubah Stock Tidak diizinkan'); 
                        }

                        if($v["price_assumption"]!=$dt['price_assumption']){                            
                            $has_value = count(array_intersect(['dp-project_material-manage-price_assumption'], $scopes));
                            if($dt["is_locked"] || !$has_value)
                            throw new Exception('Ubah Asumsi Harga Tidak diizinkan'); 
                        }

                        if($v["price_realization"]!=$dt['price_realization']){                            
                            $has_value = count(array_intersect(['dp-project_material-manage-price_realization'], $scopes));
                            if($dt["is_locked"] || !$has_value)
                            throw new Exception('Ubah Realisasi Harga Tidak diizinkan'); 
                        }

                        if($v["is_locked"]!=$dt['is_locked']){                            
                            $has_value = count(array_intersect(['dp-project_material-manage-is_locked'], $scopes));
                            if($dt["is_locked"] || !$has_value)
                            throw new Exception('Ubah Kunci Data Tidak diizinkan'); 
                        }

                        if($dt["item_code"]!=$v["item_code"]){
                            $has_value = count(array_intersect(['dp-project_material-manage-item_code'], $scopes));

                            if($dt["locked"] || !$has_value || $dt["created_by"] != $auth_id)
                            throw new Exception('Ubah Kode Item Tidak diizinkan'); 
                        
                            $v["stock"] = 0;
                            $v["price_assumption"]=0;
                        }else if($dt["item_name"]!=$v["item_name"]) {
                            $has_value = count(array_intersect(['dp-project_material-manage-item_name'], $scopes));

                            if($dt["locked"] || !$has_value || $dt["created_by"] != $auth_id)
                            throw new Exception('Ubah Nama Item Tidak diizinkan'); 
                        
                            $v["stock"] = 0;
                            $v["price_assumption"]=0;
                        }



                        ProjectMaterial::where("project_no",$model_query->no)
                        ->where("ordinal",$v["key"])->update([
                            "item_code" => $v["item_code"],
                            "unit_code" => $v["unit_code"],
                            "item_name" => $v["item_name"],
                            "qty_assumption" => $v["qty_assumption"],
                            "qty_realization" => $v["qty_realization"],
                            "stock" => $v["stock"],
                            "price_assumption" => $v["price_assumption"],
                            "price_realization" => $v["price_realization"],
                            "is_locked" => $v["is_locked"],
                            "note" => $v["note"],
                            'updated_at'=>date('Y-m-d H:i:s'),
                            'updated_by'=>$auth_id,
                        ]);
                    }

                    $ordinal++;

                }else if($v["status"]=="Add"){

                    if(!count(array_intersect(['ap-project_material_item-add'], $scopes))  )
                    throw new Exception('Tambah Project Material Item Tidak diizinkan');
    
                    if(!count(array_intersect(['dp-project_material-manage-item_code'], $scopes))  && $v["item_code"]!="")
                    throw new Exception('Tidak ada izin mengelola Kode item');
    
                    if(!count(array_intersect(['dp-project_material-manage-unit_code'], $scopes))  && $v['unit_code']!="")
                    throw new Exception('Tidak ada izin mengelola Kode unit');
                    
                    if(!count(array_intersect(['dp-project_material-manage-item_name'], $scopes)) && $v['item_name']!="")
                    throw new Exception('Tidak ada izin mengelola Nama item');
    
                    if(!count(array_intersect(['dp-project_material-manage-qty_assumption'], $scopes)) && $v['qty_assumption']!="")
                    throw new Exception('Tidak ada izin mengelola Asumsi jumlah');
                    
                    if(!count(array_intersect(['dp-project_material-manage-qty_realization'], $scopes)) && $v['qty_realization']!="")
                    throw new Exception('Tidak ada izin mengelola Realisasi jumlah');
    
                    if(!count(array_intersect(['dp-project_material-manage-stock'], $scopes)) && $v['stock']!="")
                    throw new Exception('Tidak ada izin mengelola Stock');
    
                    if(!count(array_intersect(['dp-project_material-manage-price_assumption'], $scopes)) && $v['price_assumption']!="")
                    throw new Exception('Tidak ada izin mengelola Asumsi Harga');
    
                    if(!count(array_intersect(['dp-project_material-manage-price_realization'], $scopes)) && $v['price_realization']!="")
                    throw new Exception('Tidak ada izin mengelola Realisasi Harga');
    
                    if(!count(array_intersect(['dp-project_material-manage-is_locked'], $scopes)) && $v['is_locked'])
                    throw new Exception('Tidak ada izin mengelola Kunci');

                    ProjectMaterial::insert([
                        'project_no'        =>$model_query->no,
                        'ordinal'           =>$ordinal,
                        'item_code'         =>$v["item_code"],
                        'unit_code'         =>$v['unit_code'],
                        'item_name'         =>$v['item_name'],
                        'qty_assumption'    =>$v['qty_assumption'],
                        'qty_realization'   =>$v['qty_realization'],
                        'stock'             =>$v['stock'],
                        'price_assumption'  =>$v['price_assumption'],
                        'price_realization' =>$v['price_realization'],
                        'is_locked'         =>$v['is_locked'],
                        'note'              =>$v['note'],
                        'created_at'        =>date('Y-m-d H:i:s'),
                        'created_by'        =>$auth_id,
                        'updated_at'        =>date('Y-m-d H:i:s'),
                        'updated_by'        =>$auth_id,
                    ]);
                }

                
            }

            DB::commit();
            return response()->json(
                [
                    'message' => 'Proses tambah data berhasil',
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new MyException(['message' => $e->getMessage()], 400);
            // throw new MyException(['message' => $e->getLine() ."-".$e->getMessage() ], 400);
        }
    }

    public function workers(Request $request)
    {
        MyLib::checkScope($this->auth, ['ap-project_workers-edit']);

        $project_workers_in = json_decode($request->project_workers, true);

        $rules = [
            'project_workers'                           => 'required|array',
            'project_workers.*.fullname'                => 'required',
            'project_workers.*.type'                    => 'required|in:Internal,Outsource,Support',
            'project_workers.*.working_day'             => 'required|numeric',
            'project_workers.*.cost'                    => 'required|numeric',
            'project_workers.*.day_realization'         => 'nullable|numeric',
            'project_workers.*.price_realization'       => 'nullable|numeric',
            'project_materials.*.employee'              => 'nullable|array',
            'project_materials.*.employee.no'           => 'nullable|exists:\App\Model\Employee,no',
        ];

        $messages = [
            'project_workers.required' => 'Tenaga Kerja Proyek harus diisi',
            'project_workers.array' => 'Format Tenaga Kerja Proyek Salah',
        ];

        foreach ($project_workers_in as $index => $msg) {

            $messages["project_workers.{$index}.fullname.required"]             = 'Baris #' . ($index + 1) . '. Nama tidak boleh kosong.';

            $messages["project_workers.{$index}.type.required"]                 = 'Baris #' . ($index + 1) . '. Type item tidak boleh kosong.';

            $messages["project_workers.{$index}.working_day.required"]          = 'Baris #' . ($index + 1) . '. Jumlah hari tidak boleh kosong.';
            $messages["project_workers.{$index}.working_day.numeric"]           = 'Baris #' . ($index + 1) . '. Jumlah hari harus berupa angka.';

            $messages["project_workers.{$index}.cost.required"]                 = 'Baris #' . ($index + 1) . '. Biaya tidak boleh kosong.';
            $messages["project_workers.{$index}.cost.numeric"]                  = 'Baris #' . ($index + 1) . '. Biaya harus berupa angka.';

            // $messages["project_workers.{$index}.day_realization.required"] = 'Baris #' . ($index + 1) . '. Realisasi Hari tidak boleh kosong.';
            $messages["project_workers.{$index}.day_realization.numeric"]       = 'Baris #' . ($index + 1) . '. Realisasi Hari harus berupa angka.';

            // $messages["project_workers.{$index}.price_realization.required"] = 'Baris #' . ($index + 1) . '. Realisasi Harga tidak boleh kosong.';
            $messages["project_workers.{$index}.price_realization.numeric"]     = 'Baris #' . ($index + 1) . '. Realisasi Harga harus berupa angka.';

            // $messages["project_materials.{$index}.employee_no.required"]      = 'Baris #' . ($index + 1) . '.Karyawan di Tenaga kerja Proyek harus di isi';
            $messages["project_workers.{$index}.employee.array"]                = 'Baris #' . ($index + 1) . '. Format Karyawan di Tenaga kerja proyek salah';
            $messages["project_workers.{$index}.employee.no.required"]          = "Baris #" . ($index + 1) . " . Karyawan harus di isi";
            $messages["project_workers.{$index}.employee.no.exists"]            = 'Baris #' . ($index + 1) . '. Karyawan tidak terdaftar';
        }

        $validator = \Validator::make(['project_workers' => $project_workers_in], $rules, $messages);

        if ($validator->fails()) {
            foreach ($validator->messages()->all() as $k => $v) {
                throw new MyException(['message' => $v], 400);
            }
        }

        DB::beginTransaction();
        try {
            $sql = Project::orderBy('no', 'desc')->first();
            if ($sql) {
                $no = $sql->no;
            } else {
                throw new Exception('Nomor Project Tidak Terdaftar');
            }

            $model_query = Project::where('no', $request->no)->first();

            ProjectWorker::where('project_no', $model_query->no)->delete();

            $employee_no = [];
            foreach ($project_workers_in as $key => $value) {
                $ordinal = $key + 1;

                $no_karyawan = $value['employee'] ? $value['employee']['no'] : "";
                if (in_array(strtolower($no_karyawan . $value['fullname']), $employee_no) == 1) {
                    throw new \Exception("Maaf terdapat Nama Item yang sama");
                }

                array_push($employee_no, strtolower($no_karyawan . $value['fullname']));

                // array_push($employee_no, strtolower($value['employee']['no']));

                $project_worker                     = new ProjectWorker();
                $project_worker->project_no         = $no;
                $project_worker->ordinal            = $ordinal;
                $project_worker->employee_no        = $no_karyawan == "" ? null : $no_karyawan;
                $project_worker->fullname           = $value['fullname'];
                $project_worker->type               = $value['type'];
                $project_worker->working_day        = $value['working_day'];
                $project_worker->cost               = $value['cost'];
                $project_worker->day_realization    = $value['day_realization'] == "" ? null : $value['day_realization'];
                $project_worker->price_realization  = $value['price_realization'] == "" ? null : $value['price_realization'];
                $project_worker->is_locked          = $value['is_locked'];
                $project_worker->note               = $value['note'];
                $project_worker->created_at         = date('Y-m-d H:i:s');
                $project_worker->created_by         = $this->auth->id;
                $project_worker->updated_at         = date('Y-m-d H:i:s');
                $project_worker->updated_by         = $this->auth->id;
                $project_worker->save();
            }
            DB::commit();

            return response()->json(
                [
                    'message' => 'Proses tambah data berhasil',
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new MyException(['message' => $e->getMessage()], 400);
        }
    }

    public function working_tool(Request $request)
    {
        MyLib::checkScope($this->auth, ['ap-project_working_tool-edit']);

        $project_working_tool_in = json_decode($request->project_working_tools, true);

        $rules = [
            'project_working_tools'                     => 'required|array',
            'project_working_tools.*.item_name'         => 'required',
            'project_working_tools.*.qty_assumption'    => 'required|numeric',
            'project_working_tools.*.stock'             => 'nullable|numeric',
            'project_working_tools.*.price_assumption'  => 'nullable|numeric',
            'project_working_tools.*.qty_realization'   => 'nullable|numeric',
            'project_working_tools.*.price_realization' => 'nullable|numeric',
            'project_working_tools.*.item'              => 'nullable|array',
            'project_working_tools.*.item.code'         => 'nullable|exists:\App\Model\Item,code',
            'project_working_tools.*.unit'              => 'required|array',
            'project_working_tools.*.unit.code'         => 'required|exists:\App\Model\Unit,code',
        ];

        $messages = [
            'project_working_tools.required' => 'Material Project harus diisi',
            'project_working_tools.array' => 'Format Project Material Salah',
        ];

        foreach ($project_working_tool_in as $index => $msg) {
            $messages["project_working_tools.{$index}.item_name.required"]              = 'Baris #' . ($index + 1) . '. Nama item tidak boleh kosong.';

            $messages["project_working_tools.{$index}.qty_assumption.required"]         = 'Baris #' . ($index + 1) . '. Jumlah tidak boleh kosong.';
            $messages["project_working_tools.{$index}.qty_assumption.numeric"]          = 'Baris #' . ($index + 1) . '. Jumlah harus berupa angka.';

            $messages["project_working_tools.{$index}.stock.numeric"]                   = 'Baris #' . ($index + 1) . '. Stok harus berupa angka.';

            $messages["project_working_tools.{$index}.qty_realization.numeric"]         = 'Baris #' . ($index + 1) . '. Jumlah Realisasi harus berupa angka.';

            $messages["project_working_tools.{$index}.price_assumption.numeric"]        = 'Baris #' . ($index + 1) . '. Asumsi Harga harus berupa angka.';

            $messages["project_working_tools.{$index}.price_realization.numeric"]       = 'Baris #' . ($index + 1) . '. Harga Realisasi harus berupa angka.';

            // $messages["project_working_tools.{$index}.item_code.required"]              = 'Baris #' . ($index + 1) . '. Item di project material harus di isi';
            $messages["project_working_tools.{$index}.item.array"]                      = 'Baris #' . ($index + 1) . '. Format Item di project material Salah';
            $messages["project_working_tools.{$index}.item.code.required"]              = 'Baris #' . ($index + 1) . '. Item harus di isi';
            $messages["project_working_tools.{$index}.item.code.exists"]                = 'Baris #' . ($index + 1) . '. Item tidak terdaftar';

            $messages["project_working_tools.{$index}.unit.required"]                   = 'Baris #' . ($index + 1) . '.Satuan di project material harus di isi';
            $messages["project_working_tools.{$index}.unit.array"]                      = 'Baris #' . ($index + 1) . '.Format Satuan di project material Salah';
            $messages["project_working_tools.{$index}.unit.code.required"]              = 'Baris #' . ($index + 1) . '. Satuan harus di isi';
            $messages["project_working_tools.{$index}.unit.code.exists"]                = 'Baris #' . ($index + 1) . '. Satuan tidak terdaftar';
        }

        $validator = \Validator::make(['project_working_tools' => $project_working_tool_in], $rules, $messages);

        if ($validator->fails()) {
            foreach ($validator->messages()->all() as $k => $v) {
                throw new MyException(['message' => $v], 400);
            }
        }

        DB::beginTransaction();
        try {
            $sql = Project::orderBy('no', 'desc')->first();
            if ($sql) {
                $no = $sql->no;
            } else {
                throw new Exception('Nomor Project Tidak Terdaftar');
            }

            $model_query = Project::where('no', $request->no)->first();

            ProjectWorkingTool::where('project_no', $model_query->no)->delete();


            $code_items = [];
            foreach ($project_working_tool_in as $key => $value) {
                $ordinal = $key + 1;

                $item_code = $value['item'] ? $value['item']['code'] : "";
                if (in_array(strtolower($item_code . $value['item_name']), $code_items) == 1) {
                    throw new \Exception("Maaf terdapat Nama Item yang sama");
                }

                array_push($code_items, strtolower($item_code . $value['item_name']));
                // $price_realization = 0;
                // $item_get = Item::where('code', $value['item']['code'])->first();
                // if($item_get){
                //   $price_realization = $item_get->price_realization;
                // }

                $project_working_tool                     = new ProjectWorkingTool();
                $project_working_tool->project_no         = $no;
                $project_working_tool->ordinal            = $ordinal;
                $project_working_tool->item_code          = $item_code == "" ? null : $item_code;
                $project_working_tool->unit_code          = $value['unit']['code'];
                $project_working_tool->item_name          = $value['item_name'];
                $project_working_tool->qty_assumption     = $value['qty_assumption'];
                $project_working_tool->qty_realization    = $value['qty_realization'] == "" ? null : $value['qty_realization'];
                $project_working_tool->stock              = $value['stock'] == "" ? null : $value['stock'];
                $project_working_tool->price_assumption   = $value['price_assumption'] == "" ? null : $value['price_assumption'];
                $project_working_tool->price_realization  = $value['price_realization'] == "" ? null : $value['price_realization'];
                $project_working_tool->note               = $value['note'];
                $project_working_tool->is_locked          = $value['is_locked'];
                $project_working_tool->created_at           = date('Y-m-d H:i:s');
                $project_working_tool->created_by           = $this->auth->id;
                $project_working_tool->updated_at           = date('Y-m-d H:i:s');
                $project_working_tool->updated_by           = $this->auth->id;
                $project_working_tool->save();
            }
            DB::commit();

            return response()->json(
                [
                    'message' => 'Proses tambah data berhasil',
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new MyException(['message' => $e->getMessage()], 400);
        }
    }

    public function addition(Request $request)
    {
        MyLib::checkScope($this->auth, ['ap-project_additional-edit']);

        $project_additional_in = json_decode($request->project_additionals, true);

        $rules = [
            'project_additionals'                        => 'required|array',
            'project_additionals.*.name'                 => 'required',
            'project_additionals.*.qty_assumption'       => 'nullable|numeric',
            'project_additionals.*.qty_realization'      => 'nullable|numeric',
            'project_additionals.*.unit'                 => 'required|array',
            'project_additionals.*.unit.code'            => 'required|exists:\App\Model\Unit,code',
            'project_additionals.*.price_assumption'     => 'nullable|numeric',
            'project_additionals.*.price_realization'    => 'nullable|numeric',
        ];

        $messages = [
            'project_additionals.required' => 'Tambahan proyek harus diisi',
            'project_additionals.array'    => 'Format proyek tambahan salah',
        ];

        foreach ($project_additional_in as $index => $msg) {
            $messages["project_additionals.{$index}.name.required"]       = 'Baris #' . ($index + 1) . '. Nama item tidak boleh kosong.';

            $messages["project_additionals.{$index}.qty_assumption.numeric"]          = 'Baris #' . ($index + 1) . '. Jumlah Asumsi harus berupa angka.';
            $messages["project_additionals.{$index}.qty_realization.numeric"]         = 'Baris #' . ($index + 1) . '. Jumlah Realisasi harus berupa angka.';


            $messages["project_additionals.{$index}.price_assumption.numeric"]        = 'Baris #' . ($index + 1) . '. Harga Asumsi harus berupa angka.';
            $messages["project_additionals.{$index}.price_realization.numeric"]       = 'Baris #' . ($index + 1) . '. Harga Realisasi harus berupa angka.';

            $messages["project_additionals.{$index}.unit.required"]       = 'Baris #' . ($index + 1) . '. Satuan di project tambahan harus di isi';
            $messages["project_additionals.{$index}.unit.array"]          = 'Baris #' . ($index + 1) . '. Format Satuan di project tambahan Salah';
            $messages["project_additionals.{$index}.unit.code.required"]  = 'Baris #' . ($index + 1) . '. Satuan harus di isi';
            $messages["project_additionals.{$index}.unit.code.exists"]    = 'Baris #' . ($index + 1) . '. Satuan tidak terdaftar';
        }

        $validator = \Validator::make(['project_additionals' => $project_additional_in], $rules, $messages);

        if ($validator->fails()) {
            foreach ($validator->messages()->all() as $k => $v) {
                throw new MyException(['message' => $v], 400);
            }
        }

        DB::beginTransaction();
        try {
            $sql = Project::orderBy('no', 'desc')->first();
            if ($sql) {
                $no = $sql->no;
            } else {
                throw new Exception('Nomor Project Tidak Terdaftar');
            }

            $model_query = Project::where('no', $request->no)->first();

            ProjectAdditional::where('project_no', $model_query->no)->delete();

            // $code_items = [];
            foreach ($project_additional_in as $key => $value) {
                $ordinal = $key + 1;

                // array_push($code_items, strtolower($value['working_tool']['code']));

                // $price_realization = 0;
                // $item_get = Item::where('code', $value['item']['code'])->first();
                // if($item_get){
                //   $price_realization = $item_get->price_realization;
                // }

                $project_additional                     = new ProjectAdditional();
                $project_additional->project_no         = $no;
                $project_additional->ordinal            = $ordinal;
                $project_additional->name               = $value['name'];
                $project_additional->qty_assumption     = $value['qty_assumption'];
                $project_additional->qty_realization    = $value['qty_realization']  == "" ? null : $value['qty_realization'];
                $project_additional->unit_code          = $value['unit']['code'];
                $project_additional->price_assumption   = $value['price_assumption']  == "" ? null : $value['price_assumption'];
                $project_additional->price_realization  = $value['price_realization']  == "" ? null : $value['price_realization'];
                $project_additional->is_locked          = $value['is_locked'];
                $project_additional->note               = $value['note'];
                $project_additional->created_at         = date('Y-m-d H:i:s');
                $project_additional->created_by         = $this->auth->id;
                $project_additional->updated_at         = date('Y-m-d H:i:s');
                $project_additional->updated_by         = $this->auth->id;
                $project_additional->save();
            }
            DB::commit();

            return response()->json(
                [
                    'message' => 'Proses tambah data berhasil',
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            throw new MyException(['message' => $e->getMessage()], 400);
        }
    }

    public function material_download(ProjectRequest $request)
    {
        $project = Project::where('no', $request->no)
            ->with([
                'project_materials' => function ($q) {
                    $q->with([
                        'unit',
                        'item' => function ($q2) {
                            $q2->with('unit');
                        },
                        'creator',
                        'updator',
                    ]);
                }
            ])
            ->first();
        // dd($pag);
        // return response()->json($project);

        $sendData = [
            'no_project'    => $project->no,
            'date'          => $project->date,
            'proyek'        => $project->title,
            'location'      => $project->location,
            'datas'         => $project->project_materials,
        ];
        // dd($sendData);
        // $date = new \DateTime();
        $filename = "Form Material " . $project->no;
        Pdf::setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView('pdf.material', $sendData)->setPaper('a4', 'landscape');


        $mime = MyLib::mime("pdf");
        $bs64 = base64_encode($pdf->download($filename . "." . $mime["ext"]));

        $result = [
            "contentType" => $mime["contentType"],
            "data" => $bs64,
            "dataBase64" => $mime["dataBase64"] . $bs64,
            "filename" => $filename . "." . $mime["ext"]
        ];

        return $result;
    }
}
