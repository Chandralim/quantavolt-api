<?php

namespace App\Http\Controllers\Main\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Model\Main\Member;
use App\Helpers\MyLib;
use App\Helpers\MyMember;

class MemberAccount extends Controller
{
  public function login(Request $request)
  {
    $request["username"] = strtolower($request->username);
    $rules = [
      'username' => 'required|exists:\App\Model\Main\Member,username',
      'password' => "required|min:8",
    ];

    $messages = [
      'username.required' => 'Nama Pengguna tidak boleh kosong',
      'username.exists' => 'Nama Pengguna tidak terdaftar',
      'password.required' => 'Kata Sandi tidak boleh kosong',
      'password.min' => 'Kata Sandi minimal 8 Karakter',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      throw new ValidationException($validator);
    }

    $username = $request->username;
    $password = $request->password;

    $admin = Member::where("username", $username)->first();

    if ($admin->password == null) {
      return response()->json([
        "message" => "Kata Kunci belum di atur, Hubungi Pusat"
      ], 403);
    }

    if ($admin && $admin->can_login == false) {
      return response()->json([
        "message" => "Izin Masuk Tidak Diberikan , Hubungi Pusat"
      ], 403);
    }



    if (Hash::check($password, $admin->password)) {
      $token = $admin->generateToken();

      return response()->json([
        "message" => "Berhasil login",
        "token" => $token,
      ], 200);
    } else {
      return response()->json([
        "message" => "Nama Pengguna dan Kata Sandi tidak cocok"
      ], 400);
    }
  }


  public function logout(Request $request)
  {
    $admin = MyMember::user();
    $admin->token = "";
    $admin->updated_at = date("Y-m-d H:i:s");
    $admin->save();

    return response()->json([
      "message" => "Logout Berhasil",
    ], 200);
  }

  public function checkUser(Request $request)
  {
    $p_user = MyMember::user();
    $model_query = \App\Model\Main\MemberInstitute::where("member_id", $p_user->id)->with('institute')->get();
    return response()->json([
      "message" => "Tampilkan data user",
      "user" => [
        // "id"=>$p_user->id,
        "usename" => $p_user->username,
        "email" => $p_user->email,
        "institutes" => $model_query,
        // "scope"=>($p_user->role && count($p_user->role->permissions)>0) ? $p_user->role->permissions->pluck('name') : [],
        // "roles" => $p_user->listPermissions()
      ]
    ], 200);
  }

  public function change_password(Request $request)
  {
    $admin = MyMember::user();

    $rules = [
      'old_password' => 'required|min:8|max:255',
      'password' => 'required|confirmed|min:8|max:255',
      'password_confirmation' => 'required|same:password|min:8|max:255',
    ];

    $rule = [
      "old_password.required" => "Kata Sandi lama harus diisi",
      "old_password.min" => "Kata Sandi lama minimal 8 karakter",
      "old_password.max" => "Kata Sandi lama maksimal 255 karakter",

      "password.required" => "Kata Sandi Baru harus diisi",
      "password.confirmed" => "Kata Sandi Baru tidak cocok",
      "password.min" => "Kata Sandi Baru minimal 8 karakter",
      "password.max" => "Kata Sandi Baru maksimal 255 karakter",

      "password_confirmation.required" => "Ulangi Kata Sandi Baru harus diisi",
      "password_confirmation.same" => "Ulangi Kata Sandi Baru tidak cocok",
      "password_confirmation.min" => "Ulangi Kata Sandi Baru minimal 8 karakter",
      "password_confirmation.max" => "Ulangi Kata Sandi Baru maksimal 255 karakter",
    ];

    $validator = Validator::make($request->all(), $rules, $rule);
    if ($validator->fails()) {
      throw new ValidationException($validator);
    }

    $old_password = $request->old_password;
    if (!Hash::check($old_password, $admin->password)) {
      return response()->json([
        "message" => "Kata sandi lama tidak sesuai"
      ], 400);
    }

    $admin->password = bcrypt($request->password);
    $admin->updated_at = MyLib::getMillis();
    $admin->save();

    return response()->json([
      "message" => "Kata sandi berhasil diubah",
    ], 200);
  }

  public function change_name(Request $request)
  {
    $admin = MyMember::user();

    $rules = [
      'name' => 'required|max:255',
    ];

    $rule = [
      'name.required' => 'Nama tidak boleh kosong',
      'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
    ];

    $validator = Validator::make($request->all(), $rules, $rule);
    if ($validator->fails()) {
      throw new ValidationException($validator);
    }

    $admin->name = $request->name;
    $admin->updated_at = MyLib::getMillis();
    $admin->save();

    return response()->json([
      "message" => "Nama Identitas berhasil diubah"
    ], 200);
  }
}
