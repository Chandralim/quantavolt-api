<?php

namespace App\Http\Controllers\Customer;

use App\Exceptions\MyException;
use App\Helpers\MyLib;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Model\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $auth;

    public function __construct()
    {
        $this->auth  = MyLib::user();
    }

    public function index(Request $request)
    {
        MyLib::checkScope($this->auth, ['ap-customer-view'],"Tidak ada izin melihat data karyawan");

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
        $model_query = Customer::offset($offset)->limit($limit);

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
            if(isset($sort_lists['address'])) {
                $model_query = $model_query->orderBy("address", $sort_lists["address"]);
            }
            if(isset($sort_lists['phone_number'])) {
                $model_query = $model_query->orderBy("phone_number", $sort_lists["phone_number"]);
            }
            if(isset($sort_lists['fax_number'])) {
                $model_query = $model_query->orderBy("fax_number", $sort_lists["fax_number"]);
            }
            if(isset($sort_lists['hp_number'])) {
                $model_query = $model_query->orderBy("hp_number", $sort_lists["hp_number"]);
            }
            if(isset($sort_lists['note'])) {
                $model_query = $model_query->orderBy("note", $sort_lists["note"]);
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
        $customers = $model_query->with('creator', 'updator')->get();

        return response()->json([
            'data' => CustomerResource::collection($customers)
        ], 200);
    }

    public function show(CustomerRequest $request)
    {
        MyLib::checkScope($this->auth,['ap-customer-view']);
    
        $model_query = Customer::where("code",$request->code)->first();
        return response()->json([
          "data"=>new CustomerResource($model_query),
        ],200);
    }
    
    public function store(CustomerRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-customer-add']);

        $model_query = new Customer();

        $model_query->code          = $request->code;
        $model_query->name          = $request->name;
        $model_query->address       = $request->address;
        $model_query->phone_number  = $request->phone_number;
        $model_query->hp_number     = $request->hp_number;
        $model_query->note          = $request->note;
        $model_query->created_by    = $this->auth->id;
        $model_query->created_at    = date("Y-m-d H:i:s");
        $model_query->updated_by    = $this->auth->id;
        $model_query->updated_at    = date("Y-m-d H:i:s");

        if($model_query->save())
        {
            return response()->json([
                'message'  => 'Berhasil menambahkan Customer'
            ], 200);
        }
        return response()->json([
            'message' => 'Gagal menambahkan Customer'
        ], 400);
    }

    public function update(CustomerRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-customer-edit']);
        
        $model_query = Customer::where('code', $request->code_old)->first();
        $model_query->code          = $request->code;
        $model_query->name          = $request->name;
        $model_query->address       = $request->address;
        $model_query->phone_number  = $request->phone_number;
        $model_query->hp_number     = $request->hp_number;
        $model_query->note          = $request->note;
        $model_query->created_by    = $this->auth->id;
        $model_query->created_at    = date("Y-m-d H:i:s");
        $model_query->updated_by    = $this->auth->id;
        $model_query->updated_at    = date("Y-m-d H:i:s");

        // MyLog::logging(["msgg"=>$request->work_stop_date],"check val")


        if ($model_query->save()) {
            return response()->json([
                "message"=>"Proses ubah data customer berhasil",
            ],200);
        }
        return response()->json([
            "message"=>"Proses ubah data customer gagal"
        ],400); 
    }

    public function delete(CustomerRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-customer-remove']);

        $model_query = Customer::where('code', $request->code);

        try {
            $model_query->delete();
            return response()->json([
                'message' => "Proses hapus customer berhasil",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => "Proses hapus customer gagal",
            ]);
        }
    }


}
