<?php

namespace App\Http\Controllers\Quotation;

use App\Exceptions\MyException;
use App\Helpers\MyLib;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationRequest;
use App\Http\Resources\QuotationResource;
use App\Model\Quotation;
use Illuminate\Http\Request;
use DB;

class QuotationController extends Controller
{
    private $auth;

    public function __construct(Request $request)
    {
        $this->auth = MyLib::user();
    }

    public function index(Request $request)
    {
        MyLib::checkScope($this->auth, ['ap-quotation-view']);

        $limit = 250;
        if(isset($request->limit)){
            if($request->limit <= 250){
                $limit = $request->limit;
            }else{
                throw new MyException(['message' => 'Max Limit 250']);
            }
        }

        $offset = isset($request->offset) ? (int) $request->offset : 0;

        if(isset($request->page)){
            $page = (int) $request->page;
            $offset = ($page*$limit)-$limit;
        }

        // init Model
        $model_query = Quotation::offset($offset)->limit($limit);

        if($request->sort){
            $sort_lists = [];

            $sorts = explode(",", $request->sort);
            foreach($sorts as $key => $sort){
                $side                 = explode(":", $sort);
                $side[1]              = isset($side[1]) ? $side[1] : 'ASC';
                $sort_lists[$side[0]] = $side[1]; 
            }

            if(isset($sort_lists['no'])){
                $model_query = $model_query->orderBy("no", $sort_lists["no"]);
            }
        } else {
            $model_query = $model_query->orderBy('no', 'ASC');
        }

        if(isset($request->no)){
            $model_query = $model_query->where('no', 'ilike', '%'.$request->no.'%');
        }

        $quotations = $model_query->with('creator', 'updator')->get();

        return response()->json([
            'data'  => QuotationResource::collection($quotations)
        ], 200);
    }

    public function show(QuotationRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-quotation-view']);
        $model_query = Quotation::where('no', $request->no)
        ->with([
            'quotation_details' => function ($q){
                $q->with("quotation_item");
            }
        ])
        ->first();

        return response()->json([
            'data' => new QuotationResource($model_query)
        ]);
    }

    public function store(QuotationRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-quotation-add']);


        $quotation_details_in = json_decode($request->quotation_details, true);

        dd($quotation_details_in);
        $rules = [
            'quotation_details'                         => 'required|array',
            'quotation_details.*.qty'                   => 'required|numeric|min:1',
            'quotation_details.*.quotation_item'        => 'required|array',
            'quotation_details.*.quotation_item.code'   => 'required|exists:\App\Model\QuotationItem,code',
        ];

        $messages=[
            'quotation_details.required' => 'Detail quotation harus di isi',
            'quotation_details.array' => 'Format Detail quotation Salah',

        ];

        // // Replace :index with the actual index value in the custom error messages
        foreach ($quotation_details_in as $index => $msg) {
            $messages["quotation_details.{$index}.qty.required"] = "Baris #".($index + 1).". Quantity yang diminta tidak boleh kosong.";
            $messages["quotation_details.{$index}.qty.numeric"] = "Baris #".($index + 1).".Quantity yang diminta harus angka";
            $messages["quotation_details.{$index}.qty.min"] = "Baris #".($index + 1).".Quantity minimal 1";
            $messages["quotation_details.{$index}.quotation_item.required"] = "Baris #".($index + 1).".Item di detail quotation harus di isi";
            $messages["quotation_details.{$index}.quotation_item.array"] = "Baris #".($index + 1).". Format Item di detail quotation Salah";
            $messages["quotation_details.{$index}.quotation_item.code.required"] = "Baris #".($index + 1).". Item harus di isi";
            $messages["quotation_details.{$index}.quotation_item.code.exists"] = "Baris #".($index + 1).". Item tidak terdaftar";
        }

        $validator = \Validator::make( ['quotation_details' => $quotation_details_in], $rules ,$messages);
    
        // Check if validation fails
        if ($validator->fails()) {
            foreach ($validator->messages()->all() as $k => $v) {
                throw new MyException(["message"=>$v],400);
            }
        }
        
        DB::beginTransaction();
        try {
            
            $sql = Quotation::orderBy('no','desc')->first();
            if($sql){
                $no = MyLib::nextNo($sql->no);
            }else{
                $no=MyLib::formatNo("QT");
            }
            // dd($no);
            $model_query = new Quotation();

            $model_query->no         = $no;
            $model_query->created_by = $this->auth->id;
            $model_query->created_at = date("Y-m-d H:i:s");
            $model_query->updated_by = $this->auth->id;
            $model_query->updated_at = date("Y-m-d H:i:s");  

            $model_query->save();
  
  
            $code_items=[];
            foreach ($quotation_details_in as $key => $value) {
            
                $ordinal = $key + 1;
  
                // $value["name"] = trim($value["name"]);
                // $value["unit"] = strtoupper($value["unit"]);
  
                if(in_array(strtolower($value['quotation_item']['code']),$code_items) == 1){
                    throw new \Exception("Maaf terdapat Nama Item yang sama");            
                }
                array_push($code_items,strtolower($value['quotation_item']['code']));

                $selling_price = 0;
                $quotation_item_get = \App\Model\QuotationItem::where("code",$value['quotation_item']['code'])->first();
                if($quotation_item_get){
                    $selling_price = $quotation_item_get->selling_price;
                }

                $quotation_detail                       = new \App\Model\QuotationDetail();
                $quotation_detail->quotation_no         = $no;
                $quotation_detail->quotation_item_code  = $value['quotation_item']['code'];
                $quotation_detail->qty                  = $value['qty'];
                $quotation_detail->ordinal              = $ordinal;
                $quotation_detail->selling_price        = $selling_price;
                $quotation_detail->save();
            }

            DB::commit();

            return response()->json([
                "message"=>"Proses tambah data berhasil"
            ],200);
    
        } catch (\Exception $e) {
            DB::rollback();
            throw new MyException(["message"=>$e->getMessage()],400);
        }
    }

    public function update(QuotationRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-quotation-edit']);

        $quotation_details_in = json_decode($request->quotation_details, true);

        $rules = [
            'quotation_details' => 'required|array',
            'quotation_details.*.qty' => 'required|numeric|min:1',
            'quotation_details.*.quotation_item' => 'required|array',
            'quotation_details.*.quotation_item.code' => 'required|exists:\App\Model\QuotationItem,code',
        ];

        $messages=[
            'quotation_details.required' => 'Detail quotation harus di isi',
            'quotation_details.array' => 'Format Detail quotation Salah',

        ];

        // // Replace :index with the actual index value in the custom error messages
        foreach ($quotation_details_in as $index => $msg) {
            $messages["quotation_details.{$index}.qty.required"] = "Baris #".($index + 1).". Quantity yang diminta tidak boleh kosong.";
            $messages["quotation_details.{$index}.qty.numeric"] = "Baris #".($index + 1).".Quantity yang diminta harus angka";
            $messages["quotation_details.{$index}.qty.min"] = "Baris #".($index + 1).".Quantity minimal 1";
            $messages["quotation_details.{$index}.quotation_item.required"] = "Baris #".($index + 1).".Item di detail quotation harus di isi";
            $messages["quotation_details.{$index}.quotation_item.array"] = "Baris #".($index + 1).". Format Item di detail quotation Salah";
            $messages["quotation_details.{$index}.quotation_item.code.required"] = "Baris #".($index + 1).". Item harus di isi";
            $messages["quotation_details.{$index}.quotation_item.code.exists"] = "Baris #".($index + 1).". Item tidak terdaftar";
        }

        $validator = \Validator::make( ['quotation_details' => $quotation_details_in], $rules ,$messages);
    
        // Check if validation fails
        if ($validator->fails()) {
            foreach ($validator->messages()->all() as $k => $v) {
                throw new MyException(["message"=>$v],400);
            }
        }

        

        DB::beginTransaction();
        try {
            $model_query = Quotation::where('no', $request->no)->first();
            $model_query->updated_by = $this->auth->id;
            $model_query->updated_at = date("Y-m-d H:i:s");
            $model_query->save();

            \App\Model\QuotationDetail::where("quotation_no",$model_query->no)->delete();
  
            $code_items=[];
            foreach ($quotation_details_in as $key => $value) {
            
                $ordinal = $key + 1;
  
                // $value["name"] = trim($value["name"]);
                // $value["unit"] = strtoupper($value["unit"]);
    
                if(in_array(strtolower($value['quotation_item']['code']),$code_items) == 1){
                    throw new \Exception("Maaf terdapat Nama Item yang sama");            
                }
                array_push($code_items,strtolower($value['quotation_item']['code']));

                $selling_price = 0;
                $quotation_item_get = \App\Model\QuotationItem::where("code",$value['quotation_item']['code'])->first();
                if($quotation_item_get){
                    $selling_price = $quotation_item_get->selling_price;
                }

                $quotation_detail = new \App\Model\QuotationDetail();
                $quotation_detail->quotation_no=$request->no;
                $quotation_detail->quotation_item_code = $value['quotation_item']['code'];
                $quotation_detail->qty = $value['qty'];
                $quotation_detail->ordinal = $ordinal;
                $quotation_detail->selling_price = $selling_price;
                $quotation_detail->save();
            }

            DB::commit();
    
            return response()->json([
                "message"=>"Proses tambah data berhasil"
            ],200);

        } catch (\Exception $e) {
            DB::rollback();
            throw new MyException(["message"=>$e->getMessage()],400);
        } 
    }

    public function delete(QuotationRequest $request)
    {
        MyLib::checkScope($this->auth, ['ap-quotation-remove']);

        $model_query = Quotation::where('no', $request->no);


        try {
            $model_query->delete();
            return response()->json([
                'message' => "Proses hapus nomor quotation berhasil",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => "Proses hapus nomor quotation gagal",
            ],400);
        }
    }


}
