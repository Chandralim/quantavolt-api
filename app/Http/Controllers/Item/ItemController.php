<?php

namespace App\Http\Controllers\Item;

use App\Exceptions\MyException;
use App\Helpers\MyLib;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItemRequest;
use App\Http\Resources\ItemResource;
use App\Model\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 
class ItemController extends Controller
{
    private $auth;
    public function __construct(Request $request)
    {
      $this->auth = MyLib::user();
    }
  
    public function index(Request $request)
    {
      MyLib::checkScope($this->auth,['ap-item-view']);
  
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
      $model_query = Item::offset($offset)->limit($limit);
  
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
  
        // if (isset($sort_lists["work_stop_date"])) {
        //   $model_query = $model_query->orderBy("work_stop_date",$sort_lists["work_stop_date"]);
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

      if ($request->exclude_lists) {
        $exclude_lists=json_decode($request->exclude_lists,true);
        if(count($exclude_lists)>0){          
          // $exclude_lists = array_filter($exclude_lists,function ($x){return $x != "000.00.000";});
          $model_query = $model_query->whereNotIn("code",$exclude_lists);      
        }
      }

      $model_query = $model_query->orWhere("code",MyLib::$exist_item_no);
  
      $model_query=$model_query->with('creator', 'updator', 'unit')->get();
  
      return response()->json([
        // "data"=>QuotationItemResource::collection($quotation_items->keyBy->id),
        "data"   =>ItemResource::collection($model_query),
      ],200);
    }
  
    public function show(ItemRequest $request)
    {
      MyLib::checkScope($this->auth,['ap-item-view']);
  
      $model_query = Item::where("code",$request->code)->with('creator', 'updator', 'unit')->first();
      return response()->json([
        "data"=>new ItemResource($model_query),
      ],200);
    }
  
    public function store(ItemRequest $request)
    {
      MyLib::checkScope($this->auth,['ap-item-add']);
  
      DB::beginTransaction();
  
      try {
        $model_query = new Item();
        $model_query->code            = trim($request->code);
        $model_query->name            =$request->name;
        $model_query->unit_code       =$request->unit_code;
        // $model_query->brand           =$request->brand;
        // $model_query->model           =$request->model;
        // $model_query->type            =$request->type;
        // $model_query->size            =$request->size;
        // $model_query->color           =$request->color;
        $model_query->stock_min       =$request->stock_min;
        $model_query->description     =$request->description;
        // $model_query->capital_price   =$request->capital_price;
        $model_query->created_at      =date("Y-m-d H:i:s");
        $model_query->created_by      =$this->auth->id;
        $model_query->updated_at      =date("Y-m-d H:i:s");
        $model_query->updated_by      =$this->auth->id;
        $model_query->save();
  
  
        DB::commit();
        return response()->json([
            "message"=>"Proses tambah item berhasil",
        ],200);
      } catch (\Exception $e) {
        DB::rollback();
        if($e->getCode()==1){
          return response()->json([
            "message"=>$e->getMessage(),
          ],400);
        }
  
        return response()->json([
          "message"=>"Proses tambah item gagal"
        ],400);
      }
    }
  
    public function update(ItemRequest $request)
    {
      MyLib::checkScope($this->auth,['ap-item-edit']);
  
      DB::beginTransaction();
      try {
      $model_query                  = Item::where("code",$request->code_old)->first();
      $model_query->code            = trim($request->code);
      $model_query->name            = $request->name;
      $model_query->unit_code       = $request->unit_code;
      // $model_query->brand           = $request->brand;
      // $model_query->model           = $request->model;
      // $model_query->type            = $request->type;
      // $model_query->size            = $request->size;
      // $model_query->color           = $request->color;
      $model_query->stock_min       = $request->stock_min;
      $model_query->description     = $request->description;
      // $model_query->capital_price   = $request->capital_price;
      $model_query->updated_at     = date("Y-m-d H:i:s");
      $model_query->updated_by     = $this->auth->id;
      $model_query->save();
        
        DB::commit();
        return response()->json([
          "message"=>"Proses ubah item berhasil",
        ],200);
      } catch (\Exception $e) {
        DB::rollback();
        if($e->getCode()==1){
          return response()->json([
            "message"=>$e->getMessage(),
          ],400);
        }
  
        return response()->json([
          "message"=>"Proses ubah item gagal"
        ],400);
      }
    }
  
  
    public function delete(ItemRequest $request)
    {
      MyLib::checkScope($this->auth,['ap-item-remove']);
  
      DB::beginTransaction();
      
      try {
  
        $model_query = Item::find($request->code);
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
        //throw $th;
      }
    }
}
