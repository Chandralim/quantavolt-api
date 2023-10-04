<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Support\Facades\Request;
use App\Exceptions\MyException;
use App\Model\Member;

class MyMember
{
  public static function user()
  {
    $token = Request::bearerToken();
    if ($token == "") {
      throw new MyException(["message" => "Get user info cannot complete, please restart the apps"], 400);
    }
    $model_query = \App\Model\Main\Member::where("api_token", $token)->first();
    if (!$model_query) {
      throw new MyException(["message" => "Unauthenticate"], 401);
    }
    if ($model_query->can_login == false) {
      throw new MyException(["message" => "Izin Masuk Tidak Diberikan"], 400);
    }

    return $model_query;
  }
}
