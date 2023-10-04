<?php
//app/Helpers/Envato/User.php
namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Support\Facades\Request;
use App\Exceptions\MyException;
use App\Model\User;

class MyAdmin
{
  public static function user()
  {
    $token = Request::bearerToken();
    if ($token == "") {
      throw new MyException(["message" => "Get user info cannot complete, please restart the apps"], 400);
    }
    $model_query = \App\Model\Internal\User::where("api_token", $token)->first();
    if (!$model_query) {
      throw new MyException(["message" => "Unauthenticate"], 401);
    }
    if ($model_query->can_login == false) {
      throw new MyException(["message" => "Izin Masuk Tidak Diberikan"], 400);
    }

    return $model_query;
  }


  public static function checkScope($user, $allowed_scopes = [], $msg = "Forbidden", $return = false)
  {
    $scopes = $user->listPermissions();
    $has_value = count(array_intersect($allowed_scopes, $scopes));
    if ($return) {
      return $has_value;
    }

    if ($has_value == 0) {
      throw new MyException(["message" => $msg], 403);
    }
  }

  public static function checkDataScope($user, $allowed_scopes = [])
  {
    $scopes = $user->data_permissions->pluck("in_one_line")->toArray();
    $has_value = count(array_intersect($allowed_scopes, $scopes));
    return $has_value;
  }

  public static function checkActionScope($user, $allowed_scopes = [])
  {
    $scopes = $user->action_permissions->pluck("in_one_line")->toArray();
    $has_value = count(array_intersect($allowed_scopes, $scopes));
    return $has_value;
  }
}
