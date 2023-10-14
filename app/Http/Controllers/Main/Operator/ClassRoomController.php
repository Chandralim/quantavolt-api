<?php

namespace App\Http\Controllers\Main\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\MyLib;
use App\Exceptions\MyException;
use App\Helpers\MyMember;
use Illuminate\Validation\ValidationException;
use App\Model\Main\ClassRoom;
use App\Http\Resources\Main\ClassRoomResource;
use App\Http\Requests\Main\ClassRoomRequest;
use App\Model\Main\Institute;
use App\Model\Main\Member;
use App\Model\Main\MemberInstitute;
use Exception;
use Illuminate\Support\Facades\DB;
use Image;
use File;


class ClassRoomController extends Controller
{
  private $auth;
  public function __construct(Request $request)
  {
    $this->auth = MyMember::user();
  }

  public function index(Request $request)
  {

    $link_name = $request->link_name;

    MyMember::checkRole($this->auth, $link_name, ['operator']);

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
    $model_query = ClassRoom::offset($offset)->limit($limit);

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
      $model_query = $model_query->orderBy('name', 'ASC');
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


    // if (isset($request->username)) {
    //   $model_query = $model_query->where("username", 'ilike', '%' . $request->username . '%');
    // }
    // if (isset($request->email)) {
    //   $model_query = $model_query->where("email", 'ilike', '%' . $request->email . '%');
    // }
    // if (isset($request->fullname)) {
    //   $model_query = $model_query->where("fullname", 'ilike', '%' . $request->fullname . '%');
    // }
    // if (isset($request->role)) {
    //   $model_query = $model_query->where("role", 'ilike', '%' . $request->role . '%');
    // }

    $model_query = $model_query->with('homeroom_teacher')->get();

    return response()->json([
      // "data"=>EmployeeResource::collection($employees->keyBy->id),
      "data" => ClassRoomResource::collection($model_query),
    ], 200);
  }


  public function show(ClassRoomRequest $request)
  {
    $link_name = $request->link_name;
    $id = $request->id;

    MyMember::checkRole($this->auth, $link_name, ['operator']);

    $model_query = ClassRoom::with(['homeroom_teacher'])->find($id);
    return response()->json([
      "data" => new ClassRoomResource($model_query),
    ], 200);
  }

  public function store(ClassRoomRequest $request)
  {
    $link_name = $request->link_name;
    $homeroom_teacher_id = $request->homeroom_teacher_id;

    MyMember::checkRole($this->auth, $link_name, ['operator']);


    DB::beginTransaction();
    try {
      $institute = Institute::where("link_name", $link_name)->first();

      $homeroom_teacher = MemberInstitute::where("member_id", $homeroom_teacher_id)
        ->where("institute_id", $institute->id)->where("role", "teacher")->first();
      if (!$homeroom_teacher)
        throw new Exception("Wali Kelas harus merupakan seorang guru");

      $model_query                      = new ClassRoom();
      $model_query->name                = $request->name;
      $model_query->homeroom_teacher_id = $homeroom_teacher_id;
      $model_query->institute_id = $institute->id;

      $model_query->created_at = MyLib::manualMillis(date("Y-m-d H:i:s"));
      $model_query->created_by = $this->auth->id;
      $model_query->updated_at = MyLib::manualMillis(date("Y-m-d H:i:s"));
      $model_query->updated_by = $this->auth->id;
      $model_query->save();

      DB::commit();
      return response()->json([
        "message" => "Proses tambah data berhasil",
      ], 200);
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

  public function update(ClassRoomRequest $request)
  {
    $link_name = $request->link_name;
    $id = $request->id;
    $homeroom_teacher_id = $request->homeroom_teacher_id;

    MyMember::checkRole($this->auth, $link_name, ['operator']);

    DB::beginTransaction();
    try {

      $institute = Institute::where("link_name", $link_name)->first();

      $homeroom_teacher = MemberInstitute::where("member_id", $homeroom_teacher_id)
        ->where("institute_id", $institute->id)->where("role", "teacher")->first();
      if (!$homeroom_teacher)
        throw new Exception("Wali Kelas harus merupakan seorang guru");

      $model_query                      = ClassRoom::find($id);
      $model_query->name                = $request->name;
      $model_query->homeroom_teacher_id = $homeroom_teacher_id;
      $model_query->updated_at          = MyLib::manualMillis(date("Y-m-d H:i:s"));
      $model_query->updated_by          = $this->auth->id;
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


  public function delete(ClassRoomRequest $request)
  {
    $link_name = $request->link_name;
    $id = $request->id;

    MyMember::checkRole($this->auth, $link_name, ['operator']);

    DB::beginTransaction();

    try {

      $model_query = ClassRoom::find($id);
      if (!$model_query) {
        throw new \Exception("Data tidak terdaftar", 1);
      }
      $model_query->delete();

      DB::commit();
      return response()->json([
        "message" => "Proses ubah data berhasil",
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
      //throw $th;
    }
  }
}
