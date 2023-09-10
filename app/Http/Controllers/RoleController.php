<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Helpers\MyLib;
use App\Exceptions\MyException;

use App\Model\Role;
use App\Http\Resources\RoleResource;
use App\Http\Requests\RoleRequest;

use DB;

class RoleController extends Controller
{
  public function __construct(Request $request)
  {
    $this->admin = MyLib::internalAdmin();
  }

  public function index(Request $request)
  {
    MyLib::checkScopeAdmin($this->admin,['role-view_list']);

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
    $model_query = Role::offset($offset)->limit($limit);

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
      $model_query = $model_query->orderBy('id','ASC');
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
      "data"=>RoleResource::collection($model_query),
    ],200);
  }

  // public function getProduct($store_domain,$product_domain)
  // {
  //   $data=[];

  //   if ($store_domain=="") {
  //     throw new MyException("Maaf nama toko tidak boleh kosong");
  //   }

  //   $store=SellerStore::where("domain",$store_domain)->first();
  //   if (!$store) {
  //     throw new MyException("Maaf toko tidak ditemukan");
  //   }

  //   if ($product_domain=="") {
  //     throw new MyException("Maaf nama produk tidak boleh kosong");
  //   }

  //   $product=Product::where("domain",$product_domain)->first();
  //   if (!$product) {
  //     throw new MyException("Maaf produk tidak ditemukan");
  //   }

  //   // $purchaseOrder=PurchaseOrder::sellerAvailable($store->seller->id)->first();
  //   $avaliable=PurchaseOrder::produkAvailable($store->seller->id,$product->id)->first() ? true : false;

  //   $data=[
  //     "domain"=>$product->domain,
  //     "name"=>$product->name,
  //     "price"=>$product->price,
  //     "image"=>$product->image,
  //     "available"=>$avaliable
  //   ];

  //   return response()->json([
  //     "data"=>$data
  //   ],200);

  // }
  public function show(RoleRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['role-view_detail']);

    $model_query = Role::where("id",$request->id)->with(['permissions'])->first();
    return response()->json([
      "data"=>new RoleResource($model_query),
      "permissions"=>\App\Http\Resources\PermissionResource::collection($model_query->permissions)
    ],200);
  }

  public function store(RoleRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['role-create']);

    DB::connection('pgsql')->beginTransaction();
    try {
      $model_query=new Role();
      $model_query->name=$request->name;
      $model_query->created_at=MyLib::getMillis();
      $model_query->updated_at=MyLib::getMillis();
      $model_query->save();

      $permissions = json_decode($request->permissions,true);
      if (count($permissions)==0) {
        throw new \Exception("Silahkan Pilih izin yang telah disediakan");
      }

      foreach ($permissions as $key => $value) {

        $permission_id = $value;
        $permission = \App\Model\Permission::find($permission_id);
        if(!$permission){
          throw new \Exception("Mohon Data Izin Dipilih");
        }
        $model_query->permissions()->attach($permission_id);       
      }

      DB::connection('pgsql')->commit();
      return response()->json([
        "message"=>"Proses ubah data berhasil",
      ],200);

    } catch (\Exception $e) {
      DB::connection('pgsql')->rollback();
      throw new MyException(["message"=>$e->getMessage()]);
    }    
  }

  public function update(RoleRequest $request)
  {
    MyLib::checkScopeAdmin($this->admin,['role-update']);

    DB::connection('pgsql')->beginTransaction();
    try {
      $model_query = Role::find($request->id);
      $model_query->name=$request->name;
      $model_query->updated_at=MyLib::getMillis();
      $model_query->save();

      $permissions = json_decode($request->permissions,true);

      if (count($permissions)==0) {
        throw new \Exception("Silahkan Pilih izin yang telah disediakan");
      }

      $model_query->permissions()->detach();
      // \App\Model\Role::where("goods_receipt_number",$number)->delete();

      foreach ($permissions as $key => $value) {

        $permission_id = $value;
        $permission = \App\Model\Permission::find($permission_id);
        if(!$permission){
          throw new \Exception("Mohon Data Izin Dipilih");
        }
        $model_query->permissions()->attach($permission_id);       
      }

      DB::connection('pgsql')->commit();
      return response()->json([
        "message"=>"Proses ubah data berhasil",
      ],200);

    } catch (\Exception $e) {
      DB::connection('pgsql')->rollback();
      throw new MyException(["message"=>$e->getMessage()]);
    }
  }

  //Outside
  public function permission_list(Request $request)
  {
    MyLib::checkScopeAdmin($this->admin,['role-create','role-update']);

    $model_query = \App\Model\Permission::orderBy("name","asc")->get();
    return response()->json([
      "data"=>\App\Http\Resources\PermissionResource::collection($model_query)
    ],200);
  }
}
