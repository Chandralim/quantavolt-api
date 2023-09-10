<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Helpers\MyLib;
use App\Exceptions\MyException;

use App\Model\Permission;
use App\Http\Resources\PermissionResource;
use App\Http\Requests\PermissionRequest;

class PermissionController extends Controller
{
  private $admin;

  public function __construct(Request $request)
  {
    $this->admin = MyLib::internalAdmin();
  }

  public function index(Request $request)
  {
    MyLib::checkScopeAdmin($this->admin,['permission-view_list']);

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
    $model_query = Permission::offset($offset)->limit($limit);

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

      if (isset($sort_lists["name"])) {
        $model_query = $model_query->orderBy("name",$sort_lists["name"]);
      }

      if (isset($sort_lists["id"])) {
        $model_query = $model_query->orderBy("id",$sort_lists["id"]);
      }

      // if (isset($sort_lists["role"])) {
      //   $model_query = $model_query->orderBy("role",$sort_lists["role"]);
      // }

      //
      // if (isset($sort_lists["role"])) {
      //   $model_query = $model_query->orderBy(function($q){
      //     $q->from("roles")
      //     ->select("title")
      //     ->whereColumn("id","users.role_id");
      //   },$sort_lists["role"]);
      // }

      // if (isset($sort_lists["admin"])) {
      //   $model_query = $model_query->orderBy(function($q){
      //     $q->from("users as u")
      //     ->select("u.username")
      //     ->whereColumn("u.id","users.id");
      //   },$sort_lists["admin"]);
      // }
    }else {
      $model_query = $model_query->orderBy('name','ASC');
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

      if (isset($like_lists["name"])) {
        $model_query = $model_query->orWhere("name","ilike",$like_lists["name"]);
      }

      // if (isset($like_lists["role"])) {
      //   $model_query = $model_query->orWhere("role","ilike",$like_lists["role"]);
      // }
    }

    // ==============
    // Model Filter
    // ==============
    
  //   if (isset($request->no_acc)) {
  //     $model_query = $model_query->where("no_acc",'like','%'.$request->no_acc.'%');
  //   }

    $model_query=$model_query->get();

    return response()->json([
      // "data"=>EmployeeResource::collection($employees->keyBy->id),
      "data"=>PermissionResource::collection($model_query),
    ],200);
  }


  public function show(PermissionRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['permission-view_detail']);

    $model_query = Permission::find($request->id);
    return response()->json([
      "data"=>new PermissionResource($model_query),
    ],200);
  }

  public function store(PermissionRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['permission-create']);

    $model_query=new Permission();
    $model_query->name=$request->name;
    $model_query->created_at=MyLib::getMillis();
    $model_query->updated_at=MyLib::getMillis();
    if ($model_query->save()) {
      return response()->json([
          "message"=>"Proses tambah data berhasil",
      ],200);
    }
    return response()->json([
        "message"=>"Proses tambah data gagal"
    ],400);
  }

  public function update(PermissionRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['permission-update']);
    $model_query = Permission::find($request->id);
    $model_query->name=$request->name;
    $model_query->updated_at=MyLib::getMillis();
    if ($model_query->save()) {
        return response()->json([
            "message"=>"Proses ubah data berhasil",
        ],200);
    }
    return response()->json([
        "message"=>"Proses ubah data gagal"
    ],400);
  }
}
