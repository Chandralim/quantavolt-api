<?php

namespace App\Http\Requests\Internal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstituteRequest extends FormRequest
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
        // if (request()->isMethod('post')) {
        // }
        if (request()->isMethod('get')) {
            $rules['id'] = 'required|exists:App\Model\Main\Institute,id';
        }
        if (request()->isMethod('put')) {
            $rules['id'] = 'required|exists:App\Model\Main\Institute,id';
        }
        if (request()->isMethod('post') || request()->isMethod('put')) {
            $rules['internal_marketer_id'] = 'required|exists:App\Model\Internal\User,id';
            $rules['name'] = 'required|max:255';
            $rules['address'] = 'required';
            $rules['contact_number'] = 'required|max:20';
            $rules['contact_person'] = 'required|max:50';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'id.required' => 'ID tidak boleh kosong',
            'id.exists' => 'ID tidak terdaftar',

            'name.required' => 'Nama tidak boleh kosong',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter',

            'address.required' => 'Alamat tidak boleh kosong',

            'contact_number.required' => 'Nomor Kontak tidak boleh kosong',
            'contact_number.max' => 'Nomor Kontak tidak boleh lebih dari 20 karakter',

            'contact_person.required' => 'Nomor Kontak tidak boleh kosong',
            'contact_person.max' => 'Nomor Kontak tidak boleh lebih dari 50 karakter',

            'internal_marketer_id.required' => 'Marketer ID tidak boleh kosong',
            'internal_marketer_id.exists' => 'Marketer ID tidak terdaftar',

        ];
    }
}
