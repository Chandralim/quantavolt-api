<?php

namespace App\Http\Controllers\Main\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\MyLib;
use App\Exceptions\MyException;
use Illuminate\Validation\ValidationException;
use App\Model\Main\Member;
use App\Model\Main\Institute;
use App\Model\Main\MemberInstitute;

use App\Http\Resources\Main\MemberResource;
use App\Http\Requests\Main\MemberRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use Image;
use File;

class MemberController extends Controller
{
  private $auth;
  public function __construct(Request $request)
  {
    $this->auth = \App\Helpers\MyMember::user();
  }

  public function index(Request $request)
  {

    $link_name = $request->link_name;
    $username = $request->username;

    \App\Helpers\MyMember::checkRole($this->auth, $link_name, ['operator']);

    //======================================================================================================
    // Pembatasan Data hanya memerlukan limit dan offset
    //======================================================================================================

    $limit = 30; // Limit +> Much Data
    if (isset($request->limit)) {
      if ($request->limit <= 250) {
        $limit = $request->limit;
      } else {
        throw new MyException(["message" => "Max Limit 250"]);
      }
    }

    $offset = isset($request->offset) ? (int) $request->offset : 0; // example offset 400 start from 401

    //======================================================================================================
    // Jika Halaman Ditentutkan maka $offset akan disesuaikan
    //======================================================================================================
    if (isset($request->page)) {
      $page =  (int) $request->page;
      $offset = ($page * $limit) - $limit;
    }


    //======================================================================================================
    // Init Model
    //======================================================================================================
    $model_query = Member::offset($offset)->limit($limit);

    //======================================================================================================
    // Model Sorting | Example $request->sort = "username:desc,role:desc";
    //======================================================================================================

    if ($request->sort) {
      $sort_lists = [];

      $sorts = explode(",", $request->sort);
      foreach ($sorts as $key => $sort) {
        $side = explode(":", $sort);
        $side[1] = isset($side[1]) ? $side[1] : 'ASC';
        $sort_lists[$side[0]] = $side[1];
      }

      // if (isset($sort_lists["username"])) {
      //   $model_query = $model_query->orderBy("username", $sort_lists["username"]);
      // }

      // if (isset($sort_lists["email"])) {
      //   $model_query = $model_query->orderBy("email", $sort_lists["email"]);
      // }

      // if (isset($sort_lists["fullname"])) {
      //   $model_query = $model_query->orderBy("fullname", $sort_lists["fullname"]);
      // }

      // if (isset($sort_lists["role"])) {
      //   $model_query = $model_query->orderBy("role", $sort_lists["role"]);
      // }
    } else {
      $model_query = $model_query->orderBy('username', 'ASC');
    }
    //======================================================================================================
    // Model Filter | Example $request->like = "username:%username,role:%role%,name:role%,";
    //======================================================================================================

    if ($request->like) {
      $like_lists = [];

      $likes = explode(",", $request->like);
      foreach ($likes as $key => $like) {
        $side = explode(":", $like);
        $side[1] = isset($side[1]) ? $side[1] : '';
        $like_lists[$side[0]] = $side[1];
      }

      // if (isset($like_lists["username"])) {
      //   $model_query = $model_query->orWhere("username", "ilike", $like_lists["username"]);
      // }

      // if (isset($like_lists["email"])) {
      //   $model_query = $model_query->orWhere("email", "ilike", $like_lists["email"]);
      // }

      // if (isset($like_lists["fullname"])) {
      //   $model_query = $model_query->orWhere("fullname", "ilike", $like_lists["fullname"]);
      // }

      // if (isset($like_lists["role"])) {
      //   $model_query = $model_query->orWhere("role", "ilike", $like_lists["role"]);
      // }
    }

    // ==============
    // Model Filter
    // ==============


    if (isset($request->username)) {
      $model_query = $model_query->where("username", 'ilike', '%' . $request->username . '%');
    }
    // if (isset($request->email)) {
    //   $model_query = $model_query->where("email", 'ilike', '%' . $request->email . '%');
    // }
    // if (isset($request->fullname)) {
    //   $model_query = $model_query->where("fullname", 'ilike', '%' . $request->fullname . '%');
    // }
    // if (isset($request->role)) {
    //   $model_query = $model_query->where("role", 'ilike', '%' . $request->role . '%');
    // }

    $model_query = $model_query->get();

    return response()->json([
      // "data"=>EmployeeResource::collection($employees->keyBy->id),
      "data" => MemberResource::collection($model_query),
    ], 200);
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
  // public function show(MemberRequest $request)
  // {
  //   MyLib::checkRole($this->auth, ['ap-member-view']);

  //   $model_query = Member::with(['internal_creator', 'internal_updator'])->find($request->id);
  //   return response()->json([
  //     "data" => new MemberResource($model_query),
  //   ], 200);
  // }

  public function store(MemberRequest $request)
  {

    $link_name = $request->link_name;

    \App\Helpers\MyMember::checkRole($this->auth, $link_name, ['operator']);
    $institute = Institute::where("link_name", $link_name)->first();

    DB::beginTransaction();
    try {
      $model_query                      = new Member();
      $model_query->username            = $request->username;
      $model_query->email               = MyLib::emptyStrToNull($request->email);
      $model_query->fullname            = MyLib::emptyStrToNull($request->fullname);
      $model_query->phone_number        = MyLib::emptyStrToNull($request->phone_number);

      if ($request->password)
        $model_query->password          = bcrypt($request->password);
      $model_query->can_login           = $request->can_login;

      $model_query->created_at = MyLib::manualMillis(date("Y-m-d H:i:s"));
      $model_query->created_by = $this->auth->id;
      $model_query->updated_at = MyLib::manualMillis(date("Y-m-d H:i:s"));
      $model_query->updated_by = $this->auth->id;
      $model_query->save();

      $member = Member::where('id', $model_query->id)->first();
      $member_institute = MemberInstitute::where("institute_id", $institute->id)->where("member_id", $model_query->id)->where("role", $request->create_as)->first();
      if ($member_institute) {
        return response()->json([
          "exist" => 1,
          "message" => "Anggota sudah terdaftar sebelumnya",
          "member" => new MemberResource($member)
        ], 200);
      }

      $model_query2                    = new MemberInstitute();
      $model_query2->member_id         = $member->id;
      $model_query2->institute_id      = $institute->id;
      $model_query2->role              = $request->create_as;
      $model_query2->created_at        = MyLib::manualMillis(date("Y-m-d H:i:s"));
      $model_query2->created_by        = $this->auth->id;
      $model_query2->updated_at        = MyLib::manualMillis(date("Y-m-d H:i:s"));
      $model_query2->updated_by        = $this->auth->id;
      $model_query2->save();

      DB::commit();
      return response()->json([
        "exist" => 0,
        "message" => "Proses tambah data berhasil",
        "member" => new MemberResource($member)

      ], 200);


      // DB::commit();
      // return response()->json([
      //   "message" => "Proses tambah data berhasil",
      // ], 200);
    } catch (\Exception $e) {
      DB::rollback();

      if ($e->getCode() == 1) {
        return response()->json([
          "message" => $e->getMessage(),
        ], 400);
      }

      return response()->json([
        // "message" => "Proses tambah data gagal",
        "message" => $e->getMessage(),

      ], 400);
    }
  }

  public function update(MemberRequest $request)
  {
    $link_name = $request->link_name;

    \App\Helpers\MyMember::checkRole($this->auth, $link_name, ['operator']);

    DB::beginTransaction();
    try {
      $model_query                      = Member::find($request->id);
      $model_query->username            = $request->username;
      $model_query->email               = MyLib::emptyStrToNull($request->email);
      $model_query->fullname            = MyLib::emptyStrToNull($request->fullname);
      $model_query->phone_number        = MyLib::emptyStrToNull($request->phone_number);
      if ($request->password)
        $model_query->password          = bcrypt($request->password);

      $model_query->updated_at = MyLib::manualMillis(date("Y-m-d H:i:s"));
      $model_query->updated_by = $this->auth->id;
      $model_query->save();

      DB::commit();
      return response()->json([
        "message" => "Proses ubah data berhasil",
      ], 200);
    } catch (\Exception $e) {
      DB::rollback();
      if ($e->getCode() == 1) {
        return response()->json([
          "message" => $e->getMessage(),
        ], 400);
      }
      return response()->json([
        "message" => $e->getMessage(),
      ], 400);
      return response()->json([
        "message" => "Proses ubah data gagal"
      ], 400);
    }
  }


  // public function delete(MemberRequest $request)
  // {
  //   MyLib::checkRole($this->auth, ['ap-member-remove']);

  //   DB::beginTransaction();

  //   try {

  //     $model_query = Member::find($request->id);
  //     if (!$model_query) {
  //       throw new \Exception("Data tidak terdaftar", 1);
  //     }
  //     $model_query->delete();

  //     DB::commit();
  //     return response()->json([
  //       "message" => "Proses ubah data berhasil",
  //     ], 200);
  //   } catch (\Exception  $e) {
  //     DB::rollback();
  //     if ($e->getCode() == "23503")
  //       return response()->json([
  //         "message" => "Data tidak dapat dihapus, data masih terkait dengan data yang lain nya",
  //       ], 400);

  //     if ($e->getCode() == 1) {
  //       return response()->json([
  //         "message" => $e->getMessage(),
  //       ], 400);
  //     }

  //     return response()->json([
  //       "message" => "Proses hapus data gagal",
  //     ], 400);
  //     //throw $th;
  //   }
  // }
}
