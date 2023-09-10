<?php

namespace App\Http\Controllers;

use App\Exceptions\MyException;
use App\Helpers\MyLib;
use App\Http\Requests\PbgRequest;
use App\Http\Resources\PbgResource;
use App\Model\PagDetail;
use App\Model\Pbg;
use App\Model\PbgDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class PbgController extends Controller
{
  private $auth;
  public function __construct()
  {
    $this->auth = MyLib::user();
  }

  public function index(Request $request)
  {
    MyLib::checkScope($this->auth, ['ap-pbg-view']);
    //======================================================================================================
    // Pembatasan Data hanya memerlukan limit dan offset
    //======================================================================================================

    $limit = 250; // Limit +> Much Data
    if (isset($request->limit)) {
      if ($request->limit <= 250) {
        $limit = $request->limit;
      } else {
        throw new MyException(['message' => 'Max Limit 250']);
      }
    }

    $offset = isset($request->offset) ? (int) $request->offset : 0; // example offset 400 start from 401

    //======================================================================================================
    // Jika Halaman Ditentutkan maka $offset akan disesuaikan
    //======================================================================================================
    if (isset($request->page)) {
      $page = (int) $request->page;
      $offset = $page * $limit - $limit;
    }

    //======================================================================================================
    // Init Model
    //======================================================================================================
    $model_query = Pbg::offset($offset)->limit($limit);

    //======================================================================================================
    // Model Sorting | Example $request->sort = "quotation_itemname:desc,role:desc";
    //======================================================================================================

    if ($request->sort) {
      $sort_lists = [];

      $sorts = explode(',', $request->sort);
      foreach ($sorts as $key => $sort) {
        $side = explode(':', $sort);
        $side[1] = isset($side[1]) ? $side[1] : 'ASC';
        $sort_lists[$side[0]] = $side[1];
      }

      if (isset($sort_lists['no'])) {
        $model_query = $model_query->orderBy('no', $sort_lists['no']);
      }

      // if (isset($sort_lists['title'])) {
      //     $model_query = $model_query->orderBy('title', $sort_lists['title']);
      // }

      // if (isset($sort_lists['date_start'])) {
      //     $model_query = $model_query->orderBy('date_start', $sort_lists['date_start']);
      // }

      // if (isset($sort_lists['finish_start'])) {
      //     $model_query = $model_query->orderBy('finish_start', $sort_lists['finish_start']);
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
    } else {
      $model_query = $model_query->orderBy('no', 'ASC');
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

    if (isset($request->no)) {
      $model_query = $model_query->where('no', 'ilike', '%' . $request->no . '%');
    }

    $model_query = $model_query->with('creator', 'updator', 'pag')->get();

    return response()->json(
      [
        // "data"=>QuotationItemResource::collection($quotation_items->keyBy->id),
        'data' => PbgResource::collection($model_query),
      ],
      200,
    );
  }

  public function show(PbgRequest $request)
  {
    MyLib::checkScope($this->auth, ['ap-pbg-view']);

    $model_query = Pbg::where('no', $request->no)
      ->with([
        'pag' => function ($q) {
          $q->with([
            'project',
            'pag_details' => function ($q) {
              $q->with(['item' => function ($q2) {
                $q2->with('unit');
              }]);
            }
          ]);
        },
        'pbg_details' => function ($q) {
          $q->with(['item' => function ($q2) {
            $q2->with('unit');
          }]);
        },
        'creator',
      ])
      ->first();
    return response()->json(
      [
        'data' => new PbgResource($model_query),
      ],
      200,
    );
  }

  public function store(PbgRequest $request)
  {
    MyLib::checkScope($this->auth, ['ap-pbg-add']);

    $pbg_details_in = json_decode($request->pbg_details, true);

    $rules = [
      'pbg_details'                          => 'required|array',
      'pbg_details.*.qty'                    => 'required|numeric|min:1',
      'pbg_details.*.item'                   => 'required|array',
      'pbg_details.*.item.code'              => 'required|exists:\App\Model\Item,code',
    ];

    $messages = [
      'pbg_details.required' => 'Item harus di isi',
      'pbg_details.array' => 'Format Pengeluaran Barang Salah',

    ];

    // // Replace :index with the actual index value in the custom error messages
    foreach ($pbg_details_in as $index => $msg) {



      $messages["pbg_details.{$index}.qty.required"]                  = "Baris #" . ($index + 1) . ". Jumlah yang diminta tidak boleh kosong.";
      $messages["pbg_details.{$index}.qty.numeric"]                   = "Baris #" . ($index + 1) . ". Jumlah yang diminta harus angka";
      $messages["pbg_details.{$index}.qty.min"]                       = "Baris #" . ($index + 1) . ". Jumlah minimal 1";

      $messages["pbg_details.{$index}.item.required"]                 = "Baris #" . ($index + 1) . ". Item di Form Pengambilan Barang Gudang harus di isi";
      $messages["pbg_details.{$index}.item.array"]                    = "Baris #" . ($index + 1) . ". Format Item di Pengambilan Barang Gudang Salah";
      $messages["pbg_details.{$index}.item.code.required"]            = "Baris #" . ($index + 1) . ". Item harus di isi";
      $messages["pbg_details.{$index}.item.code.exists"]              = "Baris #" . ($index + 1) . ". Item tidak terdaftar";
    }

    $validator = \Validator::make(['pbg_details' => $pbg_details_in], $rules, $messages);

    // Check if validation fails
    if ($validator->fails()) {
      foreach ($validator->messages()->all() as $k => $v) {
        throw new MyException(["message" => $v], 400);
      }
    }

    DB::beginTransaction();
    try {

      $sql = Pbg::orderBy('no', 'desc')->first();
      if ($sql) {
        $no = MyLib::nextNo($sql->no);
      } else {
        $no = MyLib::formatNo("PBG");
      }
      // dd($pbg_details_in);

      $model_query = new Pbg();
      $model_query->no            = $no;
      $model_query->date          = $request->date;
      $model_query->pag_no        = $request->pag_no;
      $model_query->created_by    = $this->auth->id;
      $model_query->created_at    = date("Y-m-d H:i:s");
      $model_query->updated_by    = $this->auth->id;
      $model_query->updated_at    = date("Y-m-d H:i:s");
      $model_query->save();


      $code_items = [];
      foreach ($pbg_details_in as $key => $value) {
        if (in_array(strtolower($value['item']['code']), $code_items) == 1) {
          throw new \Exception("Maaf terdapat Nama Item yang sama");
        }
        array_push($code_items, strtolower($value['item']['code']));
        // $pag_detail = PagDetail::where('pag_no', $request->pag_no);
        // $pag_detail = new \App\Model\PagDetail();

        $pbg_detail                    = new \App\Model\PbgDetail();
        $pbg_detail->pbg_no            = $no;
        $pbg_detail->item_code         = $value['item']['code'];
        $pbg_detail->qty               = $value['qty'];
        $pbg_detail->note              = $value['note'];
        // $pbg->is_locked         = $value['is_locked'];
        $pag_detail = PagDetail::where('pag_no', $request->pag_no)
          ->where("item_code", $value["item"]["code"])->first();

        if($pag_detail->qty < $pag_detail->qty_used + $value['qty']){
          throw new \Exception("Qty melebihi qty permintaan");
        }
        // dd($pag_detail);

        PagDetail::where('pag_no', $request->pag_no)
          ->where("item_code", $value["item"]["code"])->update(array('qty_used' => $pag_detail->qty_used + $value['qty']));

        $pbg_detail->save();
      }

      DB::commit();

      return response()->json([
        "message" => "Proses tambah data berhasil"
      ], 200);
    } catch (\Exception $e) {
      DB::rollback();
      throw new MyException(["message" => $e->getMessage()], 400);
    }
  }

  public function update(PbgRequest $request)
  {
    MyLib::checkScope($this->auth, ['ap-pbg-edit']);

    $pbg_details_in = json_decode($request->pbg_details, true);

    $rules = [
      'pbg_details'                          => 'required|array',
      'pbg_details.*.qty'                    => 'required|numeric|min:1',
      'pbg_details.*.item'                   => 'required|array',
      'pbg_details.*.item.code'              => 'required|exists:\App\Model\Item,code',
    ];

    $messages = [
      'pbg_details.required' => 'Item harus di isi',
      'pbg_details.array' => 'Format Pengeluaran Barang Salah',

    ];

    // // Replace :index with the actual index value in the custom error messages
    foreach ($pbg_details_in as $index => $msg) {

      $messages["pbg_details.{$index}.qty.required"]                  = "Baris #" . ($index + 1) . ". Jumlah yang diminta tidak boleh kosong.";
      $messages["pbg_details.{$index}.qty.numeric"]                   = "Baris #" . ($index + 1) . ". Jumlah yang diminta harus angka";
      $messages["pbg_details.{$index}.qty.min"]                       = "Baris #" . ($index + 1) . ". Jumlah minimal 1";

      $messages["pbg_details.{$index}.item.required"]                 = "Baris #" . ($index + 1) . ". Item di Form Pengambilan Barang Gudang harus di isi";
      $messages["pbg_details.{$index}.item.array"]                    = "Baris #" . ($index + 1) . ". Format Item di Pengambilan Barang Gudang Salah";
      $messages["pbg_details.{$index}.item.code.required"]            = "Baris #" . ($index + 1) . ". Item harus di isi";
      $messages["pbg_details.{$index}.item.code.exists"]              = "Baris #" . ($index + 1) . ". Item tidak terdaftar";
    }

    $validator = \Validator::make(['pbg_details' => $pbg_details_in], $rules, $messages);

    // Check if validation fails
    if ($validator->fails()) {
      foreach ($validator->messages()->all() as $k => $v) {
        throw new MyException(["message" => $v], 400);
      }
    }

    DB::beginTransaction();
    try {

      $model_query                = Pbg::where('no', $request->no)->first();

      $old_datas = Pbg::where('no', $model_query->no)
      ->leftJoin("pbg_details",function ($q){
        $q->on("pbgs.no","pbg_details.pbg_no");
      })
      ->leftJoin('pag_details',function ($q) {
        $q->on("pag_details.pag_no","pbgs.pag_no");
        $q->on("pag_details.item_code","pbg_details.item_code");
      })
      ->select( 'pbgs.pag_no','pbg_details.item_code','pbg_details.qty','pag_details.qty_used')
      ->get();


      foreach ($old_datas as $k => $v) {
        PagDetail::where('pag_no', $v->pag_no)
        ->where("item_code", $v->item_code)
        ->update(array('qty_used' => $v->qty_used - $v->qty));
      }

      $model_query->date          = $request->date;
      $model_query->pag_no        = $request->pag_no;
      $model_query->updated_by    = $this->auth->id;
      $model_query->updated_at    = date("Y-m-d H:i:s");
      $model_query->save();

      PbgDetail::where('pbg_no', $model_query->no)->delete();

      $code_items = [];
      foreach ($pbg_details_in as $key => $value) {

        if (in_array(strtolower($value['item']['code']), $code_items) == 1) {
          throw new \Exception("Maaf terdapat Nama Item yang sama");
        }
        array_push($code_items, strtolower($value['item']['code']));

        $pbg_detail                    = new \App\Model\PbgDetail();
        $pbg_detail->pbg_no            = $request->no;
        $pbg_detail->item_code         = $value['item']['code'];
        $pbg_detail->qty               = $value['qty'];
        $pbg_detail->note              = $value['note'];

        $pag_detail = PagDetail::where('pag_no', $request->pag_no)
          ->where("item_code", $value["item"]["code"])->first();
        // dd($pag_detail->qty_used - $pag_detail->qty_used + $value['qty']);

        if($pag_detail->qty < $pag_detail->qty_used + $value['qty']){
          throw new \Exception("Qty melebihi qty permintaan");
        }

        PagDetail::where('pag_no', $request->pag_no)
          ->where("item_code", $value["item"]["code"])
          ->update(array('qty_used' => $pag_detail->qty_used + $value['qty']));
        // $pbg_detail->is_locked         = $value['is_locked'];
        $pbg_detail->save();
      }

      DB::commit();

      return response()->json([
        "message" => "Proses tambah data berhasil"
      ], 200);
    } catch (\Exception $e) {
      DB::rollback();
      throw new MyException(["message" => $e->getMessage()], 400);
    }
  }

  public function delete(PbgRequest $request)
  {
    MyLib::checkScope($this->auth, ['ap-pbg-remove']);

    DB::beginTransaction();
    try {

      $model_query = Pbg::where('no', $request->no);

      if (!$model_query) {
        throw new \Exception("Data tidak terdaftar", 1);
      }
      $model_query->delete();

      DB::commit();
      return response()->json([
        "message" => "Proses delete data berhasil",
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
    }
  }

  public function download(PbgRequest $request)
  {
    $pbg = Pbg::where('no', $request->no)
      ->with([
        'pag' => function ($q) {
          $q->with([
            'project'
          ]);
        },
        'pbg_details' => function ($q) {
          $q->with(['item' => function ($q2) {
            $q2->with('unit');
          }]);
        },
        'creator',
      ])
      ->first();
    // dd($pag);
    // return response()->json($pbg);

    $sendData = [
      'pbg_no'    => $pbg->no,
      'date'      => $pbg->date,
      'pag_no'    => $pbg->pag->no,
      'date_pag'  => $pbg->pag->date,
      'proyek'    => $pbg->pag->project->title ?? "",
      'need'      => $pbg->pag->need,
      'part'      => $pbg->pag->part,
      'datas'     => $pbg->pbg_details,
    ];
    // dd($sendData);
    // $date = new \DateTime();
    $filename = $pbg->no;
    Pdf::setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);
    $pdf = PDF::loadView('pdf.pbg', $sendData)->setPaper('a4', 'landscape');


    $mime = MyLib::mime("pdf");
    $bs64 = base64_encode($pdf->download($filename . "." . $mime["ext"]));

    $result = [
      "contentType" => $mime["contentType"],
      "data" => $bs64,
      "dataBase64" => $mime["dataBase64"] . $bs64,
      "filename" => $filename . "." . $mime["ext"]
    ];

    return $result;
  }
}
