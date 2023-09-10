<?php

namespace App\Http\Controllers\WorkingTool;

use App\Exceptions\MyException;
use App\Helpers\MyLib;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkingToolRequest;
use App\Http\Resources\WorkingToolResource;
use App\Model\WorkingTool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkingToolController extends Controller
{
    private $auth;

    public function __construct()
    {
        $this->auth = MyLib::user();
    }

    public function index(Request $request)
    {
        MyLib::checkScope($this->auth, ['ap-working_tool-view']);

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
      $model_query = WorkingTool::offset($offset)->limit($limit);
  
      //======================================================================================================
      // Model Sorting | Example $request->sort = "quotation_itemname:desc,role:desc";
      //======================================================================================================
  
      if ($request->sort) {
        $sort_lists=[];
  
        $sorts=explode(",",$request->sort);
        foreach ($sorts as $key => $sort) {
            $side = explode(":",$sort);
            $side[1]=isset($side[1])?$side[1]:'ASC';
            $sort_lists[$side[0]]=$side[1];
        }
  
        if (isset($sort_lists["code"])) {
          $model_query = $model_query->orderBy("code",$sort_lists["code"]);
        }
  
        if (isset($sort_lists["name"])) {
          $model_query = $model_query->orderBy("name",$sort_lists["name"]);
        }
  
        
      }else {
        $model_query = $model_query->orderBy('code','ASC');
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
  
      if (isset($request->code)) {
        $model_query = $model_query->where("code",'ilike','%'.$request->code.'%');
      }
  
      if (isset($request->name)) {
        $model_query = $model_query->where("name",'ilike','%'.$request->name.'%');
      }
  
  
      $model_query=$model_query->with('creator', 'updator', 'unit')->get();
  
      return response()->json([
        // "data"=>QuotationItemResource::collection($quotation_items->keyBy->id),
        "data"   => WorkingToolResource::collection($model_query),
      ],200);
    }

    public function show(WorkingToolRequest $request)
    {
      MyLib::checkScope($this->auth,['ap-working_tool-view']);
  
      $model_query = WorkingTool::where("code",$request->code)->with('creator', 'updator', 'unit')->first();
      return response()->json([
        "data"=>new WorkingToolResource($model_query),
      ],200);
    }

    public function store(WorkingToolRequest $request)
    {
      MyLib::checkScope($this->auth,['ap-working_tool-add']);
  
      DB::beginTransaction();
  
      try {
        $model_query = new WorkingTool();
        $model_query->code            = trim($request->code);
        $model_query->name            =$request->name;
        $model_query->unit_code       =$request->unit_code;
        $model_query->specification   =$request->specification;
        $model_query->created_at      =date("Y-m-d H:i:s");
        $model_query->created_by      =$this->auth->id;
        $model_query->updated_at      =date("Y-m-d H:i:s");
        $model_query->updated_by      =$this->auth->id;
        $model_query->save();
  
  
        DB::commit();
        return response()->json([
            "message"=>"Proses tambah working tool berhasil",
        ],200);
      } catch (\Exception $e) {
        DB::rollback();
        if($e->getCode()==1){
          return response()->json([
            "message"=>$e->getMessage(),
          ],400);
        }
  
        return response()->json([
          "message"=>"Proses tambah working tool gagal"
        ],400);
    }
  }
  public function update(WorkingToolRequest $request)
    {
      MyLib::checkScope($this->auth,['ap-working_tool-edit']);
  
      DB::beginTransaction();
      try {
      $model_query                  = WorkingTool::where("code",$request->code_old)->first();
      $model_query->code            = trim($request->code);
      $model_query->name            = $request->name;
      $model_query->unit_code       = $request->unit_code;
      $model_query->specification   = $request->specification;
      $model_query->updated_at      = date("Y-m-d H:i:s");
      $model_query->updated_by      = $this->auth->id;
      $model_query->save();
        
        DB::commit();
        return response()->json([
          "message"=>"Proses ubah working tool berhasil",
        ],200);
      } catch (\Exception $e) {
        DB::rollback();
        if($e->getCode()==1){
          return response()->json([
            "message"=>$e->getMessage(),
          ],400);
        }
  
        return response()->json([
          "message"=>"Proses ubah working tool gagal"
        ],400);
      }
    }

    public function delete(WorkingToolRequest $request)
    {
      MyLib::checkScope($this->auth,['ap-working_tool-remove']);
  
      DB::beginTransaction();
      
      try {
  
        $model_query = WorkingTool::find($request->code);
        if(!$model_query){
          throw new \Exception("Data tidak terdaftar",1);
        }
        $model_query->delete();
  
        DB::commit();
        return response()->json([
            "message"=>"Proses delete data berhasil",
        ],200);
      } catch (\Exception  $e) {
        DB::rollback();
        if ($e->getCode()=="23503") 
        return response()->json([
          "message"=>"Data tidak dapat dihapus, data masih terkait dengan data yang lain nya",
        ],400);
  
        if($e->getCode()==1){
          return response()->json([
            "message"=>$e->getMessage(),
          ],400);
        }
  
        return response()->json([
          "message"=>"Proses hapus data gagal",
        ],400);
    }
  }
  


}
