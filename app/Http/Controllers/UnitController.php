<?php

namespace App\Http\Controllers;

use App\Exceptions\MyException;
use App\Helpers\MyLib;
use App\Http\Requests\UnitRequest;
use App\Http\Resources\UnitResource;
use App\Model\Unit;
use Illuminate\Http\Request;


class UnitController extends Controller
{
    private $auth;

    public function __construct(Request $request)
    {
        $this->auth = MyLib::user();
    }

    public function index(Request $request)
    {
        MyLib::checkScope($this->auth, ['ap-unit-view']);


        $limit = 250;
        if (isset($request->limit)) {
            if($request->limit <= 250){
                $limit = $request->limit;
            }else{
                throw new MyException(['message' => 'Max Limit 250']);
            }
        }

        $offset = isset($request->offset) ? (int) $request->offset : 0;

        // Jika Halaman DItentukan maka offset akan disesuaikan 

        if(isset($request->page)){
            $page   = (int) $request->page;
            $offset = ($page*$limit)-$limit; 
        }

        // Init model
        $model_query = Unit::offset($offset)->limit($limit);

        if($request->sort){
            $sort_lists = [];

            $sorts = explode(",", $request->sort);
            foreach($sorts as $key => $sort){
                $side                 = explode(":", $sort);
                $side[1]              = isset($side[1]) ? $side[1] : 'ASC';
                $sort_lists[$side[0]] = $side[1]; 
            }

            if(isset($sort_lists['code'])){
                $model_query = $model_query->orderBy("code", $sort_lists["code"]);
            }

            if(isset($sort_lists['name'])) {
                $model_query = $model_query->orderBy("name", $sort_lists["name"]);
            }
        } else {
            $model_query = $model_query->orderBy('code', 'ASC');
        }

        if(isset($request->code)){
            $model_query = $model_query->where('code', 'ilike', '%'.$request->code.'%');
        }

        if(isset($request->name)){
            $model_query = $model_query->where('name', 'ilike', '%'.$request->name.'%');
        }

        // $units = Unit::with("creator")->get();
        $units = $model_query->with('creator', 'updator')->get();

        return response()->json([
            'data' => UnitResource::collection($units)
        ], 200);
    }

    public function show(UnitRequest $request)
    {
        MyLib::checkScope($this->auth,['ap-unit-view']);
        $model_query = Unit::where('code', $request->code)->first();

        return response()->json([
            'data'  => new UnitResource($model_query)
        ]);
    }


    public function store(UnitRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-unit-add']);

        $model_query = new Unit();

        $model_query->code       = $request->code;
        $model_query->name       = $request->name;
        $model_query->created_by = $this->auth->id;
        $model_query->created_at = date("Y-m-d H:i:s");
        $model_query->updated_by = $this->auth->id;
        $model_query->updated_at = date("Y-m-d H:i:s");

        if($model_query->save())
        {
            return response()->json([
                'message'  => 'Berhasil menambahkan unit'
            ], 200);
        }
        return response()->json([
            'message' => 'Gagal menambahkan unit'
        ], 400);
    }   

    public function update(UnitRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-unit-edit']);
        
        $model_query = Unit::where('code', $request->code_old)->first();
        $model_query->code       = $request->code;
        $model_query->name       = $request->name;
        $model_query->updated_by  = $this->auth->id;
        $model_query->updated_at = date("Y-m-d H:i:s");

        // MyLog::logging(["msgg"=>$request->work_stop_date],"check val")


        if ($model_query->save()) {
            return response()->json([
                "message"=>"Proses ubah data unit berhasil",
            ],200);
        }
        return response()->json([
            "message"=>"Proses ubah data unit gagal"
        ],400);           
    }

    public function delete(UnitRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-unit-remove']);

        $model_query = Unit::where('code', $request->code);

        try {
            $model_query->delete();
            return response()->json([
                'message' => "Proses hapus data unit berhasil",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => "Proses hapus data unit gagal",
            ]);
        }
    }


}
