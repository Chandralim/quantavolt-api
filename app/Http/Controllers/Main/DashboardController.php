<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\MyLib;
use App\Exceptions\MyException;
use Illuminate\Validation\ValidationException;
use App\Model\Internal\Member;
use App\Http\Resources\Main\MemberInstituteResource;
use App\Model\Main\MemberInstitute;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  private $auth;
  public function __construct(Request $request)
  {
    $this->auth = \App\Helpers\MyMember::user();
  }

  public function index(Request $request)
  {

    //======================================================================================================
    // Init Model
    //======================================================================================================
    $model_query = MemberInstitute::where("member_id", $this->auth->id)->with('institute')->get();

    return response()->json([
      // "data"=>EmployeeResource::collection($employees->keyBy->id),
      // "data" => MemberInstituteResource::collection($model_query),
      "data" => $model_query,

    ], 200);
  }
}
