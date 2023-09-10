<?php

namespace App\Http\Controllers;

use App\Exceptions\MyException;
use App\Helpers\MyLib;
use App\Http\Requests\PagRequest;
use App\Http\Resources\PagResource;
use App\Model\Pag;
use App\Model\PagDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class PagController extends Controller
{
  private $auth;
  public function __construct()
  {
    $this->auth = MyLib::user();
  }

  public function index(Request $request)
  {
    MyLib::checkScope($this->auth, ['ap-pag-view']);

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
    $model_query = Pag::offset($offset)->limit($limit);

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

      // if (isset($sort_lists['title'])) {
      //     $model_query = $model_query->orderBy('title', $sort_lists['title']);
      // }

      // if (isset($sort_lists['date_start'])) {
      //     $model_query = $model_query->orderBy('date_start', $sort_lists['date_start']);
      // }

      // if (isset($sort_lists['finish_start'])) {
      //     $model_query = $model_query->orderBy('finish_start', $sort_lists['finish_start']);
      // }

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

    $forParams="";
    if(isset($request->forParams))
    {
      $forParams = json_decode($request->forParams,true);
      if(isset($forParams["exclude_not_available_qty-except_pbg_no"]))
      {
        $pbg_no = $forParams["exclude_not_available_qty-except_pbg_no"];
        if($pbg_no==""){

          $model_query = $model_query->whereIn("no",function ($q){
            $q->select('pag_no as no')
            ->from('pag_details')
            // ->where('qty',"=",0)
            ->orderBy('no')
            ->groupBy('no')
            ->havingRaw('SUM(qty) - SUM(qty_used) > 0');
          });
        }else {
          $model_query = $model_query->whereIn("no",function ($q){
            $q->select('pag_no as no')
            ->from('pag_details')
            // ->where('qty',"=",0)
            ->orderBy('no')
            ->groupBy('no')
            ->havingRaw('SUM(qty) - SUM(qty_used) > 0');
          })->orWhere("no",function ($q1) use($pbg_no){
            $q1->from("pbgs")
            ->select("pag_no")
            ->where("no",$pbg_no);
          });
        }
      }
    }

    $model_query = $model_query->with(['creator', 'updator', 'project', 'pag_details' => function ($q) {
      $q->with(['item' => function ($q2) {
        $q2->with('unit');
      }]);
    }])->get();



    return response()->json(
      [
        // "data"=>QuotationItemResource::collection($quotation_items->keyBy->id),
        'data' => PagResource::collection($model_query),
      ],
      200,
    );
  }

  public function show(PagRequest $request)
  {
    MyLib::checkScope($this->auth, ['ap-pag-view']);

    $model_query = Pag::where('no', $request->no)
      ->with([
        'pag_details' => function ($q) {
          $q->with(['item' => function ($q2) {
            $q2->with('unit');
          }]);
        },
        'project',
        'creator',
      ])
      ->first();
    return response()->json(
      [
        'data' => new PagResource($model_query),
      ],
      200,
    );
  }


  public function store(PagRequest $request)
  {
    MyLib::checkScope($this->auth, ['ap-pag-add']);

    $pag_details_in = json_decode($request->pag_details, true);
    // dd($pag_details_in);

    $rules = [
      'pag_details'                          => 'required|array',
      'pag_details.*.qty'                    => 'required|numeric|min:1',
      'pag_details.*.item'                   => 'required|array',
      'pag_details.*.item.code'              => 'required|exists:\App\Model\Item,code',
      // 'pag_details.*.unit'                   => 'required|array',
      // 'pag_details.*.unit.code'              => 'required|exists:\App\Model\Unit,code',
    ];

    $messages = [
      'pag_details.required' => 'Item harus di isi',
      'pag_details.array' => 'Format Pengambilan Barang Salah',

    ];

    // // Replace :index with the actual index value in the custom error messages
    foreach ($pag_details_in as $index => $msg) {



      $messages["pag_details.{$index}.qty.required"]                  = "Baris #" . ($index + 1) . ". Jumlah yang diminta tidak boleh kosong.";
      $messages["pag_details.{$index}.qty.numeric"]                   = "Baris #" . ($index + 1) . ". Jumlah yang diminta harus angka";
      $messages["pag_details.{$index}.qty.min"]                       = "Baris #" . ($index + 1) . ". Jumlah minimal 1";

      $messages["pag_details.{$index}.item.required"]                 = "Baris #" . ($index + 1) . ". Item di Form Pengambilan Barang Gudang harus di isi";
      $messages["pag_details.{$index}.item.array"]                    = "Baris #" . ($index + 1) . ". Format Item di Pengambilan Barang Gudang Salah";
      $messages["pag_details.{$index}.item.code.required"]            = "Baris #" . ($index + 1) . ". Item harus di isi";
      $messages["pag_details.{$index}.item.code.exists"]              = "Baris #" . ($index + 1) . ". Item tidak terdaftar";

      // $messages["pag_details.{$index}.unit.required"]                 = 'Baris #' . ($index + 1) . '. Satuan di Pengambilan Barang Gudang harus di isi';
      // $messages["pag_details.{$index}.unit.array"]                    = 'Baris #' . ($index + 1) . '. Format Satuan di Pengambilan Barang Gudang Salah';
      // $messages["pag_details.{$index}.unit.code.required"]            = 'Baris #' . ($index + 1) . '. Satuan harus di isi';
      // $messages["pag_details.{$index}.unit.code.exists"]              = 'Baris #' . ($index + 1) . '. Satuan tidak terdaftar';

    }

    $validator = \Validator::make(['pag_details' => $pag_details_in], $rules, $messages);

    // Check if validation fails
    if ($validator->fails()) {
      foreach ($validator->messages()->all() as $k => $v) {
        throw new MyException(["message" => $v], 400);
      }
    }

    DB::beginTransaction();
    try {

      $sql = Pag::orderBy('no', 'desc')->first();
      if ($sql) {
        $no = MyLib::nextNo($sql->no);
      } else {
        $no = MyLib::formatNo("PAG");
      }
      // dd($pag_details_in);

      $model_query = new Pag();
      $model_query->no            = $no;
      $model_query->project_no    = $request->project_no == "" ? null : $request->project_no;
      $model_query->need          = $request->need;
      $model_query->date          = $request->date;
      $model_query->part          = $request->part == "" ? null : $request->part;
      $model_query->created_by    = $this->auth->id;
      $model_query->created_at    = date("Y-m-d H:i:s");
      $model_query->updated_by    = $this->auth->id;
      $model_query->updated_at    = date("Y-m-d H:i:s");
      $model_query->save();


      $code_items = [];
      foreach ($pag_details_in as $key => $value) {

        $ordinal = $key + 1;

        // $value["name"] = trim($value["name"]);
        // $value["unit"] = strtoupper($value["unit"]);

        if (in_array(strtolower($value['item']['code']), $code_items) == 1) {
          throw new \Exception("Maaf terdapat Nama Item yang sama");
        }
        array_push($code_items, strtolower($value['item']['code']));

        // $selling_price = 0;
        // $quotation_item_get = \App\Model\QuotationItem::where("code",$value['quotation_item']['code'])->first();
        // if($quotation_item_get){
        //     $selling_price = $quotation_item_get->selling_price;
        // }

        $pag                    = new \App\Model\PagDetail();
        $pag->pag_no            = $no;
        $pag->item_code         = $value['item']['code'];
        $pag->qty               = $value['qty'];
        $pag->qty_used          = 0;
        // $pag->unit_code         = $value['item']['unit']['code'];
        $pag->note              = $value['note'];
        $pag->is_locked         = $value['is_locked'];
        $pag->save();
      }

      DB::commit();

      return response()->json([
        "message" => "Proses tambah data berhasil"
      ], 200);
    } catch (\Exception $e) {
      DB::rollback();
      throw new MyException(["message" => $e->getMessage()], 400);
    }
  }

  public function update(PagRequest $request)
  {
    MyLib::checkScope($this->auth, ['ap-pag-edit']);


    $pag_details_in = json_decode($request->pag_details, true);
    // dd($pag_details_in);
    $rules = [
      'pag_details'                          => 'required|array',
      'pag_details.*.qty'                    => 'required|numeric|min:1',
      'pag_details.*.item'                   => 'required|array',
      'pag_details.*.item.code'              => 'required|exists:\App\Model\Item,code',
      // 'pag_details.*.unit'                   => 'required|array',
      // 'pag_details.*.unit.code'              => 'required|exists:\App\Model\Unit,code',
    ];

    $messages = [
      'pag_details.required' => 'Detail Pengambilan Barang Gudang harus di isi',
      'pag_details.array' => 'Format Detail Pengambilan Barang Gudang Salah',

    ];

    // // Replace :index with the actual index value in the custom error messages
    foreach ($pag_details_in as $index => $msg) {

      $messages["pag_details.{$index}.qty.required"]                  = "Baris #" . ($index + 1) . ". Jumlah yang diminta tidak boleh kosong.";
      $messages["pag_details.{$index}.qty.numeric"]                   = "Baris #" . ($index + 1) . ". Jumlah yang diminta harus angka";
      $messages["pag_details.{$index}.qty.min"]                       = "Baris #" . ($index + 1) . ". Jumlah minimal 1";

      // $messages["pag_details.{$index}.item.required"]       = "Baris #".($index + 1).".Item di detail quotation harus di isi";
      $messages["pag_details.{$index}.item.array"]                    = "Baris #" . ($index + 1) . ". Format Item di Pengambilan Barang Gudang Salah";
      $messages["pag_details.{$index}.item.code.required"]            = "Baris #" . ($index + 1) . ". Item harus di isi";
      $messages["pag_details.{$index}.item.code.exists"]              = "Baris #" . ($index + 1) . ". Item tidak terdaftar";

      // $messages["pag_details.{$index}.unit.required"]                 = 'Baris #' . ($index + 1) . '. Satuan di Pengambilan Barang Gudang harus di isi';
      // $messages["pag_details.{$index}.unit.array"]                    = 'Baris #' . ($index + 1) . '. Format Satuan di Pengambilan Barang Gudang Salah';
      // $messages["pag_details.{$index}.unit.code.required"]            = 'Baris #' . ($index + 1) . '. Satuan harus di isi';
      // $messages["pag_details.{$index}.unit.code.exists"]              = 'Baris #' . ($index + 1) . '. Satuan tidak terdaftar';

    }

    $validator = \Validator::make(['pag_details' => $pag_details_in], $rules, $messages);

    // Check if validation fails
    if ($validator->fails()) {
      foreach ($validator->messages()->all() as $k => $v) {
        throw new MyException(["message" => $v], 400);
      }
    }

    DB::beginTransaction();
    try {
      $model_query                = Pag::where('no', $request->no)->first();
      $model_query->project_no    = $request->project_no == "" ? null : $request->project_no;
      $model_query->need          = $request->need == "" ? null : $request->need;
      $model_query->date          = $request->date;
      $model_query->part          = $request->part;
      $model_query->updated_by    = $this->auth->id;
      $model_query->updated_at    = date("Y-m-d H:i:s");
      $model_query->save();

      PagDetail::where('pag_no', $model_query->no)->delete();

      $code_items = [];
      foreach ($pag_details_in as $key => $value) {

        // $ordinal = $key + 1;

        // $value["name"] = trim($value["name"]);
        // $value["unit"] = strtoupper($value["unit"]);

        if (in_array(strtolower($value['item']['code']), $code_items) == 1) {
          throw new \Exception("Maaf terdapat Nama Item yang sama");
        }
        array_push($code_items, strtolower($value['item']['code']));

        $pag_detail                    = new \App\Model\PagDetail();
        $pag_detail->pag_no            = $request->no;
        $pag_detail->item_code         = $value['item']['code'];
        $pag_detail->qty               = $value['qty'];
        $pag_detail->qty_used          = 0;
        // $pag_detail->unit_code         = $value['unit']['code'];
        $pag_detail->note              = $value['note'];
        // $pag_detail->is_locked         = $value['is_locked'];
        $pag_detail->save();
      }

      DB::commit();

      return response()->json([
        "message" => "Proses tambah data berhasil"
      ], 200);
    } catch (\Exception $e) {
      DB::rollback();
      throw new MyException(["message" => $e->getMessage()], 400);
    }
  }

  public function delete(PagRequest $request)
  {
    MyLib::checkScope($this->auth, ['ap-pag-remove']);

    // $model_query = Pag::where('no', $request->no);
    // try {
    //     $model_query->delete();
    //     return response()->json([
    //         'message' => "Proses hapus Nomor Pengambilan Barang Project berhasil",
    //     ], 200);
    // } catch (\Throwable $e) {
    //     return response()->json([
    //         'message' => "Proses hapus Nomor Pengambilan Barang Project gagal",
    //     ],400);
    // }
    DB::beginTransaction();
    try {
      $model_query = Pag::where('no', $request->no);
      if (!$model_query) {
        throw new \Exception("Data tidak terdaftar", 1);
      }
      $model_query->delete();

      DB::commit();
      return response()->json([
        "message" => "Proses delete data berhasil",
      ], 200);
    } catch (\Exception  $e) {
      DB::rollback();
      if ($e->getCode() == "23503")
        return response()->json([
          "message" => "Data tidak dapat dihapus, data masih terkait dengan data yang lain nya",
        ], 400);

      if ($e->getCode() == 1) {
        return response()->json([
          "message" => $e->getMessage(),
        ], 400);
      }

      return response()->json([
        "message" => "Proses hapus data gagal",
      ], 400);
    }
  }

  public function download(PagRequest $request)
  {
    $pag = Pag::where('no', $request->no)
      ->with([
        'pag_details' => function ($q) {
          $q->with(['item' => function ($q2) {
            $q2->with('unit');
          }]);
        },
        'project',
        'creator',
      ])
      ->first();
    // dd($pag);
    // return response()->json($pag->pag_details);

    $sendData = [
      'pag_no'  => $pag->no,
      'date'    => $pag->date,
      'proyek'  => $pag->project ?? "",
      'need'    => $pag->need,
      'part'    => $pag->part,
      'datas'   => $pag->pag_details,
    ];
    // dd($sendData);
    // $date = new \DateTime();
    $filename = $pag->no;
    Pdf::setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);
    $pdf = PDF::loadView('pdf.pag', $sendData)->setPaper('a4', 'landscape');


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
