<?php

namespace App\Http\Requests\Internal;

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
            $rules['password'] = 'nullable|min:8';
            $rules['can_login'] = 'required|in:0,1';
            $rules['photo'] = 'nullable|image|mimes:jpeg|max:2048';
            $rules['phone_number'] = 'nullable|min:10|max:15';
        }
        return $rules;
    }

    // public function messages()
    // {
    //     return [
    //         'id.required' => 'ID tidak boleh kosong',
    //         'id.exists' => 'ID tidak terdaftar',

    //         'username.required_if' => '',
    //         'username.max' => 'Nama tidak boleh lebih dari 255 karakter',

    //         'address.required' => 'Alamat tidak boleh kosong',

    //         'contact_number.required' => 'Nomor Kontak tidak boleh kosong',
    //         'contact_number.max' => 'Nomor Kontak tidak boleh lebih dari 20 karakter',

    //         'contact_person.required' => 'Nomor Kontak tidak boleh kosong',
    //         'contact_person.max' => 'Nomor Kontak tidak boleh lebih dari 50 karakter',

    //         'internal_marketer_id.required' => 'Marketer ID tidak boleh kosong',
    //         'internal_marketer_id.exists' => 'Marketer ID tidak terdaftar',

    //     ];
    // }
}
