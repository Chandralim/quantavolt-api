<?php

namespace App\Http\Requests\Main;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        if (request()->isMethod('post')) {
            $rules['username'] = 'required|max:255|regex:/^\S*$/|unique:App\Model\Main\Member,username';
            $rules['email'] = 'nullable|email|max:255|unique:App\Model\Main\Member,email';
        }
        if (request()->isMethod('get')) {
            $rules['id'] = 'required|exists:App\Model\Main\Member,id';
        }
        if (request()->isMethod('put')) {
            $rules['id'] = 'required|exists:App\Model\Main\Member,id';
            $rules['username'] = 'required|max:255|unique:App\Model\Main\Member,username,' . request()->id;
            $rules['email'] = 'nullable|email|max:255|unique:App\Model\Main\Member,email,' . request()->id;
        }
        if (request()->isMethod('post') || request()->isMethod('put')) {
            // $rules['role'] = 'required';
            $rules['can_login'] = 'required|in:0,1';
            $rules['password'] = 'required_if:can_login,1|min:8';
            $rules['create_as'] = 'required|in:operator,teacher,student';
            $rules['link_name'] = 'required|exists:\App\Model\Main\Institute,link_name';

            // $rules['photo'] = 'nullable|image|mimes:jpeg|max:2048';
            $rules['phone_number'] = 'nullable|min:10|max:15|unique:App\Model\Main\Member,phone_number';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'id.required' => 'ID tidak boleh kosong',
            'id.exists' => 'ID tidak terdaftar',

            'username.required' => 'Nama Pengguna harus diisi',
            'username.max' => 'Nama Pengguna tidak boleh lebih dari 255 karakter',
            'username.regex' => 'Nama Pengguna tidak boleh ada spasi',
            'username.unique' => 'Nama Pengguna telah terdaftar',

            'email.email' => 'Format Email salah',
            'email.unique' => 'Email telah terdaftar',

            'can_login.required' => 'Izin masuk tidak boleh kosong',
            'can_login.in' => 'Izin masuk harus dipilih',

            'password.required_if' => 'Kata Sandi tidak boleh kosong',
            'password.min' => 'Kata Sandi tidak boleh kurang dari 8 karakter',


            'create_as.required' => 'Sebagai tidak boleh kosong',
            'create_as.in' => 'Sebagai harus dipilih',

            'link_name.required' => 'Info Kunci tidak boleh kosong',
            'link_name.exists' => 'Info Kunci tidak terdaftar',

            'phone_number.required' => 'Nomor HP tidak boleh kosong',
            'phone_number.unique' => 'Nomor HP telah terdaftar',

        ];
    }
}
