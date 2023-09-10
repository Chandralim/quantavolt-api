<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Exceptions\MyException;
use Illuminate\Validation\ValidationException;

use App\Helpers\MyLib;
use App\Helpers\MyLog;
use Illuminate\Validation\Rule;


class TestController extends Controller
{
  public function index(Request $request)
  {
    // 1667408400
    // 1667463804137
    return strtotime("2022-11-03 00:00:00");

    // $test=0;
    // dd($test);
    // for ($i=1; $i <=36 ; $i++) { 
    //   $datas = new \App\Model\Main\EventFile();
    //   $datas->event_id = 2;
    //   $datas->no = $i;
    //   $datas->image = "files/artifindo/events/2022_palmex-".$i.".JPG";
    //   $datas->save();
    // }
  }

  public function post(Request $request)
  {
    MyLog::logging("test","FlowMeter");
    return response()->json([
      "data"=>"OK",
    ],200);
  }
}
