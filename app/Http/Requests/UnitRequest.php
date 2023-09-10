<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
        if(request()->isMethod('post'))
        {
            $rules['code'] = 'required||unique:App\Model\Unit,code|regex:/^[a-zA-Z]+$/';
            $rules['name'] = 'required|max:255|regex:/^[a-zA-Z ]+$/';
        }

        if(request()->isMethod('get'))
        {
            $rules['code'] = 'required|exists:App\Model\Unit,code';
        }

        if (request()->isMethod('put')) {
            $rules['code_old'] = 'required|regex:/^[a-zA-Z]+$/|exists:App\Model\Unit,code';
            $rules['code'] = 'required|regex:/^[a-zA-Z]+$/|unique:App\Model\Unit,code,'.request()->code_old;
            $rules['name'] = 'required|max:255|regex:/^[a-zA-Z ]+$/';
        }

        return $rules;
    }


    public function messages()
    {
        return [
            'code.required'  => 'Code wajib diisi',
            'code.unique'    => 'Code sudah terdaftar',
            'code.regex'     => 'Code harus huruf',

            'name.required'  => 'Nama unit wajib diisi',
            'name.regex'     => 'Nama unit hanya boleh huruf',
        ];
    }


}
