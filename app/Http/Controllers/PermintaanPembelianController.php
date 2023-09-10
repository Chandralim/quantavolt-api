<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Helpers\MyLib;
use App\Exceptions\MyException;

use App\Model\PermintaanPembelian;
use App\Http\Resources\PermintaanPembelianResource;
use App\Http\Requests\PermintaanPembelianRequest;
use DB;

use App\Exports\MyReport;
use Excel;

class PermintaanPembelianController extends Controller
{
  private $admin;

  public function __construct(Request $request)
  {
    $this->admin = MyLib::internalAdmin();
  }

  public function index(Request $request,$download=false)
  {
    MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-view_list']);

    //======================================================================================================
    // Pembatasan Data hanya memerlukan limit dan offset
    //======================================================================================================

    $limit = 250; // Limit +> Much Data
    if (isset($request->limit)) {
      if ($request->limit <= 250) {
        $limit = $request->limit;
      }else {
        throw new MyException(["message"=>"Max Limit 250"]);
      }
    }

    $offset = isset($request->offset) ? (int) $request->offset : 0; // example offset 400 start from 401

    //======================================================================================================
    // Jika Halaman Ditentutkan maka $offset akan disesuaikan
    //======================================================================================================
    if (isset($request->page)) {
      $page =  (int) $request->page;
      $offset = ($page*$limit)-$limit;
    }


    //======================================================================================================
    // Init Model
    //======================================================================================================
    $model_query = new \App\Model\PermintaanPembelian();
    if (!$download) {
      $model_query = $model_query->offset($offset)->limit($limit);
    }
    //======================================================================================================
    // Model Sorting | Example $request->sort = "username:desc,role:desc";
    //======================================================================================================

    if ($request->sort) {
      $sort_lists=[];

      $sorts=explode(",",$request->sort);
      foreach ($sorts as $key => $sort) {
        $side = explode(":",$sort);
        $side[1]=isset($side[1])?$side[1]:'ASC';
        $sort_lists[$side[0]]=$side[1];
      }

      if (isset($sort_lists["no"])) {
        $model_query = $model_query->orderBy("no",$sort_lists["no"]);
      }

      if (isset($sort_lists["created_at"])) {
        $model_query = $model_query->orderBy("created_at",$sort_lists["created_at"]);
      }

      if (isset($sort_lists["reject_by"])) {
        // $model_query = $model_query->orderBy("reject_by",$sort_lists["reject_by"]);
        $model_query = $model_query->orderBy(function($q){
              $q->from("internal.admins as u")
              ->select("u.name")
              ->whereColumn("id","internal.permintaan_pembelians.reject_by");
            },$sort_lists["reject_by"]);
      }

      if (isset($sort_lists["submit_by"])) {
        // $model_query = $model_query->orderBy("submit_by",$sort_lists["submit_by"]);
        $model_query = $model_query->orderBy(function($q){
              $q->from("internal.admins as u")
              ->select("u.name")
              ->whereColumn("id","internal.permintaan_pembelians.submit_by");
            },$sort_lists["submit_by"]);
      }

      if (isset($sort_lists["check_by"])) {
        // $model_query = $model_query->orderBy("check_by",$sort_lists["check_by"]);
        $model_query = $model_query->orderBy(function($q){
              $q->from("internal.admins as u")
              ->select("u.name")
              ->whereColumn("id","internal.permintaan_pembelians.check_by");
            },$sort_lists["check_by"]);
      }

      if (isset($sort_lists["approve_by"])) {
        // $model_query = $model_query->orderBy("approve_by",$sort_lists["approve_by"]);
        $model_query = $model_query->orderBy(function($q){
              $q->from("internal.admins as u")
              ->select("u.name")
              ->whereColumn("id","internal.permintaan_pembelians.approve_by");
            },$sort_lists["approve_by"]);
      }

      // if (isset($sort_lists["admin"])) {
      //   $model_query = $model_query->orderBy(function($q){
      //     $q->from("users as u")
      //     ->select("u.username")
      //     ->whereColumn("u.id","users.id");
      //   },$sort_lists["admin"]);
      // }
    }else {
      $model_query = $model_query->orderBy('no','desc');
    }
    //======================================================================================================
    // Model Filter | Example $request->like = "username:%username,role:%role%,name:role%,";
    //======================================================================================================

    if ($request->like) {
      $like_lists=[];

      $likes=explode(",",$request->like);
      foreach ($likes as $key => $like) {
        $side = explode(":",$like);
        $side[1]=isset($side[1])?$side[1]:'';
        $like_lists[$side[0]]=$side[1];
      }

      if (isset($like_lists["no"])) {
        $model_query = $model_query->orWhere("no","ilike",$like_lists["no"]);
      }

    }

    // ==============
    // Model Filter
    // ==============
      
    if (isset($request->no)) {
      $model_query = $model_query->where("no",'like','%'.$request->no.'%');
    }

    $model_query=$model_query->get();

    return response()->json([
      "data"=>PermintaanPembelianResource::collection($model_query),
    ],200);
  }


  public function show(PermintaanPembelianRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-view_detail']);

    $model_query = PermintaanPembelian::where("no",$request->no)
    ->with([
      "permintaan_pembelian_details"=>function($q){
        $q->with(['creator','updator']);
      }
    ])
    ->first();
    return response()->json([
      "data"=>new PermintaanPembelianResource($model_query),
    ],200);
  }

  public function store(PermintaanPembelianRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-create']);

    $no_urut="0001";
    $formatNo=MyLib::formatNo("PP");
    $no = $formatNo[0]."/".$formatNo[1];

    DB::connection('pgsql')->beginTransaction();
      try {
        // throw new \Exception(MyLib::nextNo("09220001/PP/ARTI/IX/2022"));
        $sql = PermintaanPembelian::where("no","like",'%'.$formatNo[1])->orderBy('no','desc')->first();
        if($sql){
          $no = MyLib::nextNo($sql->no);
        }

        $model_query=new PermintaanPembelian();
        $model_query->no=$no;
        $model_query->note=$request->note;
        $model_query->created_at=MyLib::getMillis();
        $model_query->updated_at=MyLib::getMillis();


        if ($request->submit == 1) {
          if(MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-submit'],true) == 0)
          throw new \Exception("Maaf Anda Tidak memiliki otoritas pengajuan");
          
          $model_query->submit_by=$request->submit_by;
          $model_query->submit_at=MyLib::getMillis();
        }

        $model_query->save();

        $permintaan_pembelian_details = json_decode($request->permintaan_pembelian_details,true);

        if ($request->submit==1  && (!$permintaan_pembelian_details || count($permintaan_pembelian_details)==0)) {
          throw new \Exception("Silahkan masukkan data detail");
        }

        $name_items=[];
        foreach ($permintaan_pembelian_details as $key => $value) {
          $ordinal = $key + 1;

          $value["name"] = trim($value["name"]);
          $value["unit"] = strtoupper($value["unit"]);

          $rules = [
            'name' => 'required|max:255',
            'qty' => 'required|numeric|min:1',
            'unit' => 'required|max:255',
            'price' => 'required|numeric|min:1',
            'supplier_name' => 'nullable|max:255',
            'note' => 'nullable|max:255',
          ];

          $messages=[
            'name.required' => 'Nama Item harus di isi',
            'name.max' => 'Nama Item maksimal 255 karakter',

            'qty.required' => 'Quantity yang diminta tidak boleh kosong',
            'qty.numeric' => 'Quantity yang diminta harus angka',
            'qty.min' => 'Quantity minimal 1',

            'unit.required' => 'Satuan harus di isi',
            'unit.max' => 'Satuan maksimal 255 karakter',

            'price.required' => 'Quantity yang diminta tidak boleh kosong',
            'price.numeric' => 'Quantity yang diminta harus angka',
            'price.min' => 'Harga minimal 1',

            'supplier_name.required' => 'Nama Supplier harus di isi',
            'supplier_name.max' => 'Nama Supplier maksimal 255 karakter',

            'note.required' => 'Keterangan harus di isi',
            'note.max' => 'Keterangan maksimal 255 karakter',
          ];

          $validator = \Validator::make($value,$rules,$messages);
          if ($validator->fails()) {
            foreach ($validator->messages()->all() as $k => $v) {
              throw new \Exception("Baris Data Ke-".$ordinal." ".$v);
            }
          }

          if(in_array(strtolower($value['name']),$name_items) == 1){
            throw new \Exception("Maaf terdapat Nama Item yang sama");            
          }
          array_push($name_items,strtolower($value['name']));

          $permintaan_pembelian_detail = new \App\Model\PermintaanPembelianDetail();
          $permintaan_pembelian_detail->created_at=MyLib::getMillis();
          $permintaan_pembelian_detail->updated_at=MyLib::getMillis();
          $permintaan_pembelian_detail->permintaan_pembelian_no=$no;
          $permintaan_pembelian_detail->name = $value['name'];
          $permintaan_pembelian_detail->qty = $value['qty'];
          $permintaan_pembelian_detail->unit = $value['unit'];
          $permintaan_pembelian_detail->price = $value['price'];
          $permintaan_pembelian_detail->supplier_name = $value['supplier_name'];
          $permintaan_pembelian_detail->note = $value['note'];
          $permintaan_pembelian_detail->created_by = $this->admin->id;
          $permintaan_pembelian_detail->updated_by = $this->admin->id;
          $permintaan_pembelian_detail->ordinal = $ordinal;
          $permintaan_pembelian_detail->save();
        }

        DB::connection('pgsql')->commit();

        return response()->json([
          "message"=>"Proses tambah data berhasil"
        ],200);

      } catch (\Exception $e) {
        DB::connection('pgsql')->rollback();
        throw new MyException(["message"=>$e->getMessage()]);
      }
  }

  public function update(PermintaanPembelianRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-update']);

    DB::connection('pgsql')->beginTransaction();
    try {
      $model_query = PermintaanPembelian::where('no',$request->no)->first();
      
      if ($model_query->submit_by!=null || $model_query->check_by!=null || $model_query->approve_by!=null)
      throw new \Exception("Maaf Form Sedang Diproses");
      

      //$request->submit //0 draft //1 Diajukan
      if ($request->submit==1) {
        
        if(MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-submit'],true) == 0)
        throw new \Exception("Maaf Anda Tidak memiliki otoritas pengajuan");

        if ($request->reject_note) 
        throw new \Exception("Hapus Pesan Penolakan sebelum melanjutkan");
        

        $model_query->reject_by=null;
        $model_query->reject_at=null;

        $model_query->submit_by=$this->admin->id;
        $model_query->submit_at=MyLib::getMillis();
      }

      $model_query->note=$request->note;
      $model_query->reject_note=$request->reject_note;
      $model_query->updated_at=MyLib::getMillis();
      $model_query->save();        
      
      $permintaan_pembelian_details = json_decode($request->permintaan_pembelian_details,true);
  
      
      if ($request->submit==1  && (!$permintaan_pembelian_details || count($permintaan_pembelian_details)==0))
      throw new \Exception("Silahkan masukkan data detail");
      

      if ($permintaan_pembelian_details && count($permintaan_pembelian_details)>0) 
      \App\Model\PermintaanPembelianDetail::where("permintaan_pembelian_no",$model_query->no)->delete();
      


      $name_items=[];
      foreach ($permintaan_pembelian_details as $key => $value) {
        $ordinal = $key + 1;

        $value["unit"] = strtoupper($value["unit"]);
        $value["name"] = trim($value["name"]);

        $rules = [
          'name' => 'required|max:255',
          'qty' => 'required|numeric|min:1',
          'unit' => 'required|max:255',
          'price' => 'required|numeric|min:1',
          'supplier_name' => 'nullable|max:255',
          'note' => 'nullable|max:255',
        ];

        $messages=[
          'name.required' => 'Nama Item harus di isi',
          'name.max' => 'Nama Item maksimal 255 karakter',

          'qty.required' => 'Quantity yang diminta tidak boleh kosong',
          'qty.numeric' => 'Quantity yang diminta harus angka',
          'qty.min' => 'Quantity minimal 1',

          'unit.required' => 'Satuan harus di isi',
          'unit.max' => 'Satuan maksimal 255 karakter',

          'price.required' => 'Quantity yang diminta tidak boleh kosong',
          'price.numeric' => 'Quantity yang diminta harus angka',
          'price.min' => 'Harga minimal 1',

          'supplier_name.required' => 'Nama Supplier harus di isi',
          'supplier_name.max' => 'Nama Supplier maksimal 255 karakter',

          'note.required' => 'Keterangan harus di isi',
          'note.max' => 'Keterangan maksimal 255 karakter',
        ];

        $validator = \Validator::make($value,$rules,$messages);
        if ($validator->fails()) {
          foreach ($validator->messages()->all() as $k => $v) {
            throw new \Exception("Baris Data Ke-".$ordinal." ".$v);
          }
        }

        if(in_array(strtolower($value['name']),$name_items) == 1)
        throw new \Exception("Maaf terdapat Nama Item yang sama");            
        
        array_push($name_items,strtolower($value['name']));

        if ($model_query->reject_by==null && $value['reject_note'])
        throw new \Exception("Maaf Anda tidak diizinkan untuk menulis pesan penolakan");
        
        if ($request->submit==1 && $value['reject_note'])
        throw new \Exception("Hapus Pesan Penolakan Masing-Masing Item sebelum melanjutkan");
        
        $permintaan_pembelian_detail = new \App\Model\PermintaanPembelianDetail();
        $permintaan_pembelian_detail->created_at=MyLib::getMillis();
        $permintaan_pembelian_detail->updated_at=MyLib::getMillis();
        $permintaan_pembelian_detail->permintaan_pembelian_no=$model_query->no;
        $permintaan_pembelian_detail->name = $value['name'];
        $permintaan_pembelian_detail->qty = $value['qty'];
        $permintaan_pembelian_detail->unit = $value['unit'];
        $permintaan_pembelian_detail->price = $value['price'];
        $permintaan_pembelian_detail->supplier_name = $value['supplier_name'];
        $permintaan_pembelian_detail->note = $value['note'];
        $permintaan_pembelian_detail->reject_note = $value['reject_note'];
        $permintaan_pembelian_detail->created_by = $this->admin->id;
        $permintaan_pembelian_detail->updated_by = $this->admin->id;
        $permintaan_pembelian_detail->ordinal = $ordinal;
        $permintaan_pembelian_detail->save();
      }

      DB::connection('pgsql')->commit();

      return response()->json([
        "message"=>"Proses ubah data berhasil"
      ],200);

    } catch (\Exception $e) {
      DB::connection('pgsql')->rollback();
      throw new MyException(["message"=>$e->getMessage()],400);
    }
  }


  public function reject(PermintaanPembelianRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-check','permintaan_pembelian-approve']);

    DB::connection('pgsql')->beginTransaction();
    try {
      $model_query = PermintaanPembelian::where('no',$request->no)->first();
      if ($model_query->reject_by)
      throw new \Exception("Maaf Form ini telah di tolak ");
      

      if ($model_query->submit_by!=null && $model_query->check_by!=null && $model_query->approve_by!=null)
      throw new \Exception("Maaf Form Sudah tidak dapat ditolak");
      
      $model_query->submit_by = null;
      $model_query->submit_at = null;

      $model_query->check_by = null;
      $model_query->check_at = null;

      $model_query->approve_by = null;
      $model_query->approve_at = null;

      $model_query->reject_by = $this->admin->id;
      $model_query->reject_at = MyLib::getMillis();
      $model_query->reject_note=$request->reject_note;

      $model_query->updated_at=MyLib::getMillis();
      $model_query->save();        

      $permintaan_pembelian_details = json_decode($request->permintaan_pembelian_details,true);
  
      if (!$permintaan_pembelian_details || count($permintaan_pembelian_details)==0) {
        throw new \Exception("Silahkan masukkan data detail");
      }

      $name_items=[];
      foreach ($permintaan_pembelian_details as $key => $value) {
        $ordinal = $key + 1;
        $rules = [
          'reject_note' => 'nullable|max:255',
        ];

        $messages=[
          'reject_note.required' => 'Pesan penolakan harus di isi',
          'reject_note.max' => 'Pesan penolakan maksimal 255 karakter',
        ];

        $validator = \Validator::make($value,$rules,$messages);
        if ($validator->fails()) {
          foreach ($validator->messages()->all() as $k => $v) {
            throw new \Exception("Baris Data Ke-".$ordinal." ".$v);
          }
        }
          
        $permintaan_pembelian_detail = \App\Model\PermintaanPembelianDetail::where("permintaan_pembelian_no",$model_query->no)->where("ordinal",$ordinal)->update(
          [
            "updated_by"=>$this->admin->id,
            "updated_at"=>MyLib::getMillis(),
            "reject_note"=>$value['reject_note'],
          ]
        );
      }
      DB::connection('pgsql')->commit();
      return response()->json([
        "message"=>"Proses penolakan data berhasil"
      ],200);

    } catch (\Exception $e) {
      DB::connection('pgsql')->rollback();
      throw new MyException(["message"=>$e->getMessage()]);
    }
  }


  public function process_acc(PermintaanPembelianRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-check','permintaan_pembelian-approve']);

    DB::connection('pgsql')->beginTransaction();
    try {
      $model_query = PermintaanPembelian::where('no',$request->no)->first();
      if ($model_query->reject_by)
      throw new \Exception("Maaf Form ini sedang di tolak ");
      
      if ($model_query->submit_by==null)
      throw new \Exception("Maaf Form ini belum diajukan ");
      
      if ($model_query->submit_by!=null && $model_query->check_by!=null && $model_query->approve_by!=null)
      throw new \Exception("Maaf Form Sudah terkunci");
      

      $used = 0;
      if($model_query->check_by==null && MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-check'],true) == 0)
      throw new \Exception("Maaf Anda Tidak memiliki otoritas pengajuan");
      

      if($model_query->check_by==null && MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-check'],true) > 0){
        $model_query->check_by = $this->admin->id;
        $model_query->check_at = MyLib::getMillis();  
        $used=1;
      }
      if ($used==0) {
        if($model_query->approve_by==null && MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-approve'],true) == 0)
        throw new \Exception("Maaf Anda Tidak memiliki otoritas pengajuan");
        
  
        if($model_query->approve_by==null && MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-approve'],true) > 0){
          $model_query->approve_by = $this->admin->id;
          $model_query->approve_at = MyLib::getMillis();  
        }
      }

      $model_query->updated_at=MyLib::getMillis();
      $model_query->save();        

      DB::connection('pgsql')->commit();
      return response()->json([
        "message"=>"Proses Acc data berhasil"
      ],200);

    } catch (\Exception $e) {
      DB::connection('pgsql')->rollback();
      throw new MyException(["message"=>$e->getMessage()]);
    }
  }

  public function sub_download(Request $request)
    {
      MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-check']);

      $rules = [
        'no' => 'required|exists:\App\Model\PermintaanPembelian,no',
      ];

      $messages=[
        'no.required' => 'No harus di isi',
        'no.exists' => 'No tidak terdaftar',
      ];

      $validator = \Validator::make($request->all(),$rules,$messages);
      if ($validator->fails()) {
        throw new ValidationException($validator);
      }

      $data = PermintaanPembelian::where('no',$request->no)->first();
    
      $date = new \DateTime();
      $filename=$date->format("YmdHis").'-permintaan-pembelian';
    
      // $mime=MyLib::mime("xlsx");
      // $bs64=base64_encode(Excel::raw(new MyReport($data,'report.permintaan_pembelian'), $mime["exportType"]));

      $mime=MyLib::mime("pdf");
      $bs64=base64_encode(Excel::raw(new MyReport($data,'report.permintaan_pembelian'), $mime["exportType"]));

      $result =[
        "contentType"=>$mime["contentType"],
        "data"=>$bs64,
        "dataBase64"=>$mime["dataBase64"].$bs64,
        "filename"=>$filename
      ];
      return $result;
      // return $data;
    }

  // public function download(Request $request)
  //   {
  //     MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-check','permintaan_pembelian-approve']);
    
  //     $data = json_decode(json_encode($this->index($request,true)),true)["original"]["data"];
    
  //     $date = new \DateTime();
  //     $filename=$date->format("YmdHis").'-bank_list';
    
  //     $mime=MyLib::mime("xlsx");
  //     $bs64=base64_encode(Excel::raw(new MyReport($data,'report.bank_list'), $mime["exportType"]));
    
  //     $result =[
  //       "contentType"=>$mime["contentType"],
  //       "data"=>$bs64,
  //       "dataBase64"=>$mime["dataBase64"].$bs64,
  //       "filename"=>$filename
  //     ];
  //     return $result;
  //     // return $data;
  //   }


  // public function update(PermintaanPembelianRequest $request)
  // {
  //   MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-view_detail']);

  //   DB::connection('pgsql')->beginTransaction();
  //   try {
  //     $model_query = PermintaanPembelian::where('no',$request->no)->first();
      
  //     $change_note=0;
  //     if( MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-update'],true) > 0 ){
  //       $model_query->note=$request->note;
  //       $model_query->reject_note=null;
  //       $model_query->reject_by=null;
  //       $change_note=1;
  //     }

  //     //Check punya hak untuk mengajukan
  //     if ( MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-submit'],true) > 0 ) {
  //       if ($model_query->submit_by==null && $request->submit==1) {
  //         $model_query->submit_by=$this->admin->id;
  //         $model_query->submit_status=1;
  //         $model_query->submit_at=MyLib::getMillis();
  //       }else if ($model_query->submit_by==$this->admin->id && $request->submit==0) {
  //         $model_query->submit_by=null;
  //         $model_query->submit_status=null;
  //         $model_query->submit_at=null;
  //       }
  //     }

  //     if($change_note==1 && $model_query->submit_by !== null && $model_query->submit_by !== $this->admin->id){
  //       throw new \Exception("Ubah data ditolak. Form ini telah diajukan");
  //     }

  //     if($change_note==1 && $model_query->submit_by !== null && $model_query->submit_by == $this->admin->id && MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-submit'],true) == 0  ){
  //       throw new \Exception("Ubah data ditolak. Anda sudah tidak memiliki otoritas");
  //     }

  //     if ( MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-check'],true) > 0 ) {
  //       if ($model_query->check_by==null && $request->check==1) {
  //         $model_query->check_by=$this->admin->id;
  //         $model_query->check_status=1;
  //         $model_query->check_at=MyLib::getMillis();
  //       }else if ($model_query->check_by==$this->admin->id && $request->check==0) {
  //         $model_query->check_by=null;
  //         $model_query->check_status=null;
  //         $model_query->check_at=null;
  //       }
  //     }
  //     // throw new \Exception(MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-approve'],true));
      
  //     if ( MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-approve'],true) > 0 ) {
  //       if ($model_query->approve_by==null && $request->approve==1) {
  //         $model_query->approve_by=$this->admin->id;
  //         $model_query->approve_status=1;
  //         $model_query->approve_at=MyLib::getMillis();
  //       }else if ($model_query->approve_by==$this->admin->id && $request->approve==0) {
  //         $model_query->approve_by=null;
  //         $model_query->approve_status=null;
  //         $model_query->approve_at=null;
  //       }
  //     }

      
  //     if ($model_query->check_by!=null && $model_query->submit_by==null) {
  //       throw new \Exception("Maaf , Pemeriksaan telah ditandai namun pengajuan belum ditandai");
  //     }
  //     if ($model_query->approve_by!=null && $model_query->check_by==null) {
  //       throw new \Exception("Maaf , Persetujuan telah ditandai namun pemeriksaan belum ditandai");
  //     }
  //     // throw new \Exception($model_query->submit_by."x".$model_query->check_by."x".$model_query->approve_by);


  //     $model_query->updated_at=MyLib::getMillis();
  //     $model_query->save();        
  //     // $data=GoodsReceipt::where('number',$number)->first();

  //     // if ($data->checker_code) {
  //     //   throw new \Exception("GR sudah disetujui, tidak dapat diubah lagi");
  //     // }
      
  //     $permintaan_pembelian_details = json_decode($request->permintaan_pembelian_details,true);
  
  //     if (!$permintaan_pembelian_details || count($permintaan_pembelian_details)==0) {
  //       throw new \Exception("Silahkan masukkan data detail");
  //     }

  //     if(MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian_detail-update'],true) > 0 ){
  //       // $old_ppd = \App\Model\PermintaanPembelianDetail::where("permintaan_pembelian_no",$model_query->no)->get();
  //       // $old_ppds=\App\Http\Resources\PermintaanPembelianDetailResource::collection($old_ppd);


  //       // throw new MyException(["message"=>array_diff($old_ppds,$permintaan_pembelian_details)]);

  //       // throw new \Exception(json_encode(["0"=>$old_ppds,"1"=>$permintaan_pembelian_details]));



  //       \App\Model\PermintaanPembelianDetail::where("permintaan_pembelian_no",$model_query->no)->delete();
  //       $name_items=[];
  //       foreach ($permintaan_pembelian_details as $key => $value) {
  //         $ordinal = $key + 1;
  
  //         $value["unit"] = strtoupper($value["unit"]);
  //         $value["name"] = trim($value["name"]);
  
  //         $rules = [
  //           'name' => 'required|max:255',
  //           'qty' => 'required|numeric|min:1',
  //           'unit' => 'required|max:255',
  //           'price' => 'required|numeric|min:1',
  //           'supplier_name' => 'nullable|max:255',
  //           'note' => 'nullable|max:255',
  //         ];
  
  //         $messages=[
  //           'name.required' => 'Nama Item harus di isi',
  //           'name.max' => 'Nama Item maksimal 255 karakter',
  
  //           'qty.required' => 'Quantity yang diminta tidak boleh kosong',
  //           'qty.numeric' => 'Quantity yang diminta harus angka',
  //           'qty.min' => 'Quantity minimal 1',
  
  //           'unit.required' => 'Satuan harus di isi',
  //           'unit.max' => 'Satuan maksimal 255 karakter',
  
  //           'price.required' => 'Quantity yang diminta tidak boleh kosong',
  //           'price.numeric' => 'Quantity yang diminta harus angka',
  //           'price.min' => 'Harga minimal 1',
  
  //           'supplier_name.required' => 'Nama Supplier harus di isi',
  //           'supplier_name.max' => 'Nama Supplier maksimal 255 karakter',
  
  //           'note.required' => 'Keterangan harus di isi',
  //           'note.max' => 'Keterangan maksimal 255 karakter',
  //           ];
  
  //         $validator = \Validator::make($value,$rules,$messages);
  //         if ($validator->fails()) {
  //           foreach ($validator->messages()->all() as $k => $v) {
  //             throw new \Exception("Baris Data Ke-".$ordinal." ".$v);
  //           }
  //         }

  //         if(in_array(strtolower($value['name']),$name_items) == 1){
  //           throw new \Exception("Maaf terdapat Nama Item yang sama");            
  //         }
  //         array_push($name_items,strtolower($value['name']));

            
  //         $permintaan_pembelian_detail = new \App\Model\PermintaanPembelianDetail();
  //         $permintaan_pembelian_detail->created_at=MyLib::getMillis();
  //         $permintaan_pembelian_detail->updated_at=MyLib::getMillis();
  //         $permintaan_pembelian_detail->permintaan_pembelian_no=$model_query->no;
  //         $permintaan_pembelian_detail->name = $value['name'];
  //         $permintaan_pembelian_detail->qty = $value['qty'];
  //         $permintaan_pembelian_detail->unit = $value['unit'];
  //         $permintaan_pembelian_detail->price = $value['price'];
  //         $permintaan_pembelian_detail->supplier_name = $value['supplier_name'];
  //         $permintaan_pembelian_detail->note = $value['note'];
  //         // $permintaan_pembelian_detail->checked = $value['checked'];
  //         $permintaan_pembelian_detail->created_by = $this->admin->id;
  //         $permintaan_pembelian_detail->updated_by = $this->admin->id;
  //         $permintaan_pembelian_detail->ordinal = $ordinal;
  //         $permintaan_pembelian_detail->save();
  
  //       }
  //     }
  //     DB::connection('pgsql')->commit();

  //     return response()->json([
  //       "message"=>"Proses ubah data berhasil"
  //     ],200);

  //   } catch (\Exception $e) {
  //     DB::connection('pgsql')->rollback();
  //     throw new MyException(["message"=>$e->getMessage()]);
  //   }
  // }
  // public function update(PermintaanPembelianRequest $request)
  // {
  //   MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-view_detail']);

  //   DB::connection('pgsql')->beginTransaction();
  //   try {
  //     $model_query = PermintaanPembelian::where('no',$request->no)->first();

  //     if( MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-update'],true) > 0 ){
        
  //       if ($model_query->submit_by!=null) {
  //         if ($model_query->submit_by!=$this->admin->id) {
  //           throw new \Exception("Pengubahan ditolak. Form ini telah diajukan");
  //         }

  //         if (MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-submit'],true) == 0) {
  //           throw new \Exception("Maaf Otoritas Pengajuan Tidak Lagi Valid");
  //         }
  //       }

  //       if ($model_query->approve_by!=null) {
  //         if ($model_query->approve_by!=$this->admin->id) {
  //           throw new \Exception("Pengubahan ditolak. Form ini telah disetujui");
  //         }

  //         if (MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-approve'],true) == 0) {
  //           throw new \Exception("Maaf Otoritas Persetujuan Tidak Lagi Valid");
  //         }
  //       }

      
  //       $model_query->note=$request->note;
  //     }

  //     //Check punya hak untuk mengajukan
  //     if (MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-submit'],true) > 0) {
  //       // check apakah ada request pengajuan  dan sudah ada yang mengajukan sebelumnya dan bukan kita
  //       if ($request->submit && $model_query->submit_by!==null && $model_query->submit_by!==$this->admin->id) {
  //         throw new \Exception("Pengajuan ditolak. Form ini telah diajukan orang lain");
  //       }

  //       $model_query->submit_by=$request->submit==1?$this->admin->id:null;
  //       $model_query->submit_at=$request->submit==1?MyLib::getMillis():null;            
  //     }

  //     // if (MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-submit'],true) > 0 && ($model_query->submit_by==null || $model_query->submit_by==$this->admin->id)) {
  //     //   $model_query->submit_by=$request->submit==1?$this->admin->id:null;
  //     //   $model_query->submit_at=$request->submit==1?MyLib::getMillis():null;
  //     // }

  //     //Check punya hak untuk menyetujui
  //     if (MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-approve'],true) > 0) {

  //       // check apakah sebelum ny sudah diajukan
  //       if ($model_query->submit_by==null) {
  //         throw new \Exception("Persetujuan ditolak. Form ini belum diajukan");
  //       }

  //       // check apakah ada request pengajuan  dan sudah ada yang menyetujui sebelumnya dan bukan kita
  //       if ($request->approve && $model_query->approve_by!==null && $model_query->approve_by!==$this->admin->id) {
  //         throw new \Exception("Persetujuan ditolak. Form ini telah disetujui orang lain");
  //       }
        
  //       $model_query->approve_by=$request->approve==1?$this->admin->id:null;
  //       $model_query->approve_at=$request->approve==1?MyLib::getMillis():null;            
  //     }
      
  //     // if (MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-approve'],true) > 0 && ($model_query->approve_by==null || $model_query->approve_by==$this->admin->id) && $model_query->submit_by) {
  //     //   $model_query->approve_by=$request->approve==1?$this->admin->id:null;
  //     //   $model_query->approve_at=$request->approve==1?MyLib::getMillis():null;
  //     // } 

  //     //Check punya hak untuk memeriksa
  //     if (MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-check'],true) > 0) {

  //       // check apakah sebelum ny sudah disetujui
  //       if ($model_query->approve_by==null) {
  //         throw new \Exception("Pemeriksaan ditolak. Form ini belum disetujui");
  //       }

  //       // check apakah ada request pengajuan  dan sudah ada yang memeriksa sebelumnya dan bukan kita
  //       if ($request->check && $model_query->check_by!==null && $model_query->check_by!==$this->admin->id) {
  //         throw new \Exception("Pemeriksaan ditolak. Form ini telah diperiksa orang lain");
  //       }
        
  //       $model_query->check_by=$request->check==1?$this->admin->id:null;
  //       $model_query->check_at=$request->check==1?MyLib::getMillis():null;            
  //     }
  //     // if (MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian-check'],true) > 0 && ($model_query->check_by==null || $model_query->check_by==$this->admin->id) && $model_query->approve_by) {
  //     //   $model_query->check_by=$request->check==1?$this->admin->id:null;
  //     //   $model_query->check_at=$request->check==1?MyLib::getMillis():null;
  //     // }         

  //     $model_query->updated_at=MyLib::getMillis();
  //     $model_query->save();        
  //     // $data=GoodsReceipt::where('number',$number)->first();

  //     // if ($data->checker_code) {
  //     //   throw new \Exception("GR sudah disetujui, tidak dapat diubah lagi");
  //     // }
      
  //     $permintaan_pembelian_details = json_decode($request->permintaan_pembelian_details,true);
  
  //     if (!$permintaan_pembelian_details || count($permintaan_pembelian_details)==0) {
  //       throw new \Exception("Silahkan masukkan data detail");
  //     }

  //     if(MyLib::checkScopeAdmin($this->admin,['permintaan_pembelian_detail-view_detail','permintaan_pembelian_detail-check'],true) > 0 ){

        
  
  //       // if (\App\Model\GoodsReceiptDetail::where("approved_qty",">",0)->where("goods_receipt_number",$number)->first()) {
  //       //   throw new \Exception("Maaf purchase request sudah di approved , data sudah tidak dapat di ubah");
  //       // }
        
  
  //       \App\Model\PermintaanPembelianDetail::where("permintaan_pembelian_no",$model_query->no)->delete();
  
  //       foreach ($permintaan_pembelian_details as $key => $value) {
  //         $ordinal = $key + 1;
  
  //         $value["unit"] = strtoupper($value["unit"]);
  
  //         $rules = [
  //           'name' => 'required|max:255',
  //           'qty' => 'required|numeric|min:1',
  //           'unit' => 'required|max:255',
  //           'price' => 'required|numeric|min:1',
  //           'supplier_name' => 'nullable|max:255',
  //           'note' => 'nullable|max:255',
  //           // 'checked' => 'nullable|in:0,1',
  //         ];
  
  //         $messages=[
  //           'name.required' => 'Nama Item harus di isi',
  //           'name.max' => 'Nama Item maksimal 255 karakter',
  
  //           'qty.required' => 'Quantity yang diminta tidak boleh kosong',
  //           'qty.numeric' => 'Quantity yang diminta harus angka',
  //           'qty.min' => 'Quantity minimal 1',
  
  //           'unit.required' => 'Satuan harus di isi',
  //           'unit.max' => 'Satuan maksimal 255 karakter',
  
  //           'price.required' => 'Quantity yang diminta tidak boleh kosong',
  //           'price.numeric' => 'Quantity yang diminta harus angka',
  //           'price.min' => 'Harga minimal 1',
  
  //           'supplier_name.required' => 'Nama Supplier harus di isi',
  //           'supplier_name.max' => 'Nama Supplier maksimal 255 karakter',
  
  //           'note.required' => 'Keterangan harus di isi',
  //           'note.max' => 'Keterangan maksimal 255 karakter',
  
  //           // 'checked.required' => 'Centang harus di click',
  //           // 'checked.in' => 'Centang harus di click',
  //         ];
  
  //         $validator = \Validator::make($value,$rules,$messages);
  //         if ($validator->fails()) {
  //           foreach ($validator->messages()->all() as $k => $v) {
  //             throw new \Exception("Baris Data Ke-".$ordinal." ".$v);
  //           }
  //         }
            
  //         $permintaan_pembelian_detail = new \App\Model\PermintaanPembelianDetail();
  //         $permintaan_pembelian_detail->created_at=MyLib::getMillis();
  //         $permintaan_pembelian_detail->updated_at=MyLib::getMillis();
  //         $permintaan_pembelian_detail->permintaan_pembelian_no=$model_query->no;
  //         $permintaan_pembelian_detail->name = $value['name'];
  //         $permintaan_pembelian_detail->qty = $value['qty'];
  //         $permintaan_pembelian_detail->unit = $value['unit'];
  //         $permintaan_pembelian_detail->price = $value['price'];
  //         $permintaan_pembelian_detail->supplier_name = $value['supplier_name'];
  //         $permintaan_pembelian_detail->note = $value['note'];
  //         // $permintaan_pembelian_detail->checked = $value['checked'];
  //         $permintaan_pembelian_detail->created_by = $this->admin->id;
  //         $permintaan_pembelian_detail->updated_by = $this->admin->id;
  //         $permintaan_pembelian_detail->ordinal = $ordinal;
  //         $permintaan_pembelian_detail->save();
  
  //       }
  //     }


  //     DB::connection('pgsql')->commit();

  //     return response()->json([
  //       "message"=>"Proses ubah data berhasil"
  //     ],200);

  //   } catch (\Exception $e) {
  //     DB::connection('pgsql')->rollback();
  //     throw new MyException($e->getMessage());
  //   }
  // }
}
