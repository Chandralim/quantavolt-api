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

  public static function checkRole($user, $link_name = '', $allowed_scopes = [], $msg = "Forbidden", $return = false)
  {

    if ($link_name == "") return 0;

    $member_institutes = \App\Model\Main\MemberInstitute::where("member_id", $user->id)->where("institute_id", function ($q) use ($link_name) {
      $q->select('id');
      $q->from('institutes');
      $q->where("link_name", $link_name);
    })->get()->pluck('role')->toArray();

    $has_value = count(array_intersect($allowed_scopes, $member_institutes));
    if ($return) {
      return $has_value;
    }

    if ($has_value == 0) {
      throw new MyException(["message" => $msg], 403);
    }
  }
}
