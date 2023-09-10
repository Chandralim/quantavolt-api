<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Helpers\MyLib;
use App\Exceptions\MyException;
use App\Helpers\MyLog;
use App\Model\Employee;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\EmployeeRequest;

class EmployeeController extends Controller
{
  private $auth;
  public function __construct(Request $request)
  {
    $this->auth = MyLib::user();
  }

  public function index(Request $request)
  {
    MyLib::checkScope($this->auth,['ap-employee-view'],"Tidak ada izin melihat data karyawan");

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
    $model_query = Employee::offset($offset)->limit($limit);

    //======================================================================================================
    // Model Sorting | Example $request->sort = "employeename:desc,role:desc";
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

      if (isset($sort_lists["fullname"])) {
        $model_query = $model_query->orderBy("fullname",$sort_lists["fullname"]);
      }

      if (isset($sort_lists["handphone_number"])) {
        $model_query = $model_query->orderBy("handphone_number",$sort_lists["handphone_number"]);
      }

      
      if (isset($sort_lists["work_start_date"])) {
        $model_query = $model_query->orderBy("work_start_date",$sort_lists["work_start_date"]);
      }

      if (isset($sort_lists["work_stop_date"])) {
        $model_query = $model_query->orderBy("work_stop_date",$sort_lists["work_stop_date"]);
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
      //     $q->from("employees as u")
      //     ->select("u.employeename")
      //     ->whereColumn("u.id","employees.id");
      //   },$sort_lists["auth"]);
      // }
    }else {
      $model_query = $model_query->orderBy('no','ASC');
    }
    //======================================================================================================
    // Model Filter | Example $request->like = "employeename:%employeename,role:%role%,name:role%,";
    //======================================================================================================

    // if ($request->like) {
    //   $like_lists=[];

    //   $likes=explode(",",$request->like);
    //   foreach ($likes as $key => $like) {
    //     $side = explode(":",$like);
    //     $side[1]=isset($side[1])?$side[1]:'';
    //     $like_lists[$side[0]]=$side[1];
    //   }

    //   if (isset($like_lists["employeename"])) {
    //     $model_query = $model_query->orWhere("employeename","ilike",$like_lists["employeename"]);
    //   }

    //   // if (isset($like_lists["role"])) {
    //   //   $model_query = $model_query->orWhere("role","ilike",$like_lists["role"]);
    //   // }
    // }

    // ==============
    // Model Filter
    // ==============
    
    if (isset($request->no)) {
      $model_query = $model_query->where("no",'ilike','%'.$request->no.'%');
    }

    if (isset($request->fullname)) {
      $model_query = $model_query->where("fullname",'ilike','%'.$request->fullname.'%');
    }

    if (isset($request->handphone_number)) {
      $model_query = $model_query->where("handphone_number",'ilike','%'.$request->handphone_number.'%');
    }


    if ($request->excludes) {
      $excludes=explode(",",$request->excludes);

      if(in_array("employee_had_which_user_id",$excludes)){
        $model_query = $model_query->whereNull("which_user_id");
      }
      
    }

    $model_query=$model_query->get();

    return response()->json([
      // "data"=>EmployeeResource::collection($employees->keyBy->id),
      "data"=>EmployeeResource::collection($model_query),
    ],200);
  }

  public function show(EmployeeRequest $request)
  {
    MyLib::checkScope($this->auth,['ap-employee-view']);

    $model_query = Employee::where("no",$request->no)->first();
    return response()->json([
      "data"=>new EmployeeResource($model_query),
    ],200);
  }

  public function store(EmployeeRequest $request)
  {
    MyLib::checkScope($this->auth,['ap-employee-add']);

    $model_query=new Employee();

    $model_query->no                = $request->no;
    $model_query->nik               = $request->nik;
    $model_query->fullname          = $request->fullname;
    $model_query->birth_date        = $request->birth_date;
    $model_query->address           = $request->address;
    $model_query->handphone_number  = $request->handphone_number;
    $model_query->work_start_date   = $request->work_start_date;
    $model_query->work_stop_date    = $request->work_stop_date;

    $model_query->created_at        = date("Y-m-d H:i:s");
    $model_query->created_by        = $this->auth->id;
    $model_query->updated_at        = date("Y-m-d H:i:s");
    $model_query->updated_by        = $this->auth->id;
    if ($model_query->save()) {
      return response()->json([
          "message"=>"Proses tambah data berhasil",
      ],200);
    }
    return response()->json([
        "message"=>"Proses tambah data gagal"
    ],400);
  }

  public function update(EmployeeRequest $request)
  {
    MyLib::checkScope($this->auth,['ap-employee-edit']);

    $model_query                    = Employee::where("no",$request->no_old)->first();
    $model_query->no                = $request->no;
    $model_query->nik               = $request->nik;
    $model_query->fullname          = $request->fullname;
    $model_query->birth_date        = $request->birth_date;
    $model_query->address           = $request->address;
    $model_query->handphone_number  = $request->handphone_number;
    $model_query->work_start_date   = $request->work_start_date;
    $model_query->work_stop_date    = $request->work_stop_date;

    $model_query->updated_at        = date("Y-m-d H:i:s");
    $model_query->updated_by        = $this->auth->id;
    if ($model_query->save()) {
        return response()->json([
            "message"=>"Proses ubah data berhasil",
        ],200);
    }
    return response()->json([
        "message"=>"Proses ubah data gagal"
    ],400);
  }


  public function delete(EmployeeRequest $request)
  {
    MyLib::checkScope($this->auth,['ap-employee-remove']);

    $model_query = Employee::where("no",$request->no);

    try {
      $model_query->delete();
      return response()->json([
          "message"=>"Proses hapus data berhasil",
      ],200);
    } catch (\Exception  $e) {
      if ($e->getCode()=="23503") 
      return response()->json([
        "message"=>"Data tidak dapat dihapus, data masih terkait dengan data yang lain nya",
      ],400);

      return response()->json([
        "message"=>"Proses hapus data gagal",
      ],400);
      //throw $th;
    }
    // if ($model_query->delete()) {
    //     return response()->json([
    //         "message"=>"Proses ubah data berhasil",
    //     ],200);
    // }

    // return response()->json([
    //     "message"=>"Proses ubah data gagal",
    // ],400);
  }
}
