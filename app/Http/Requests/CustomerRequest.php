<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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

        if(request()->isMethod('put'))
        {
            $rules['code_old']     = 'required|regex:/^\S*$/|exists:App\Model\Customer,code';
            $rules['code']         = 'required|regex:/^\S*$/|unique:App\Model\Unit,code,'.request()->code_old;
        }
        if(request()->isMethod('get'))
        {
            $rules['code']         = 'required|exists:App\Model\Customer,code';
        }
        if (request()->isMethod('post') || request()->isMethod('put')) {
            $rules['name']         = 'required|max:255|regex:/^[a-zA-Z ]+$/';
            $rules['address']      = 'required';
            $rules['phone_number'] = 'required|numeric|digits_between:10,15';
            $rules['hp_number']    = 'required|numeric|digits_between:10,15';
            $rules['note']         = 'nullable|max:255';
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
        'code.required'         => 'Code wajib diisi',
        'code.unique'           => 'Code sudah terdaftar',
        'code.regex'            => 'Code tidak boleh ada spasi',
            
        'name.required'         => 'Nama wajib diisi',
        'name.max'              => 'Nama tidak boleh lebih dari 255 karakter',
        'name.reges'            => 'Nama hanya boleh huruf',
        
        'address.required'      => 'Address wajib diisi',

        'phone_number.required'       => 'Phone number wajib diisi',
        'phone_number.digits_between' => 'Phone number harus sebanyak 10 hingga 15 digit',
        'phone_number.numeric'        => 'Phone Number harus berupa angka',
        
        'hp_number.required'           => 'Phone number wajib diisi',
        'hp_number.digits_between'     => 'Phone number harus sebanyak 10 hingga 15 digit',
        'hp_number.numeric'            => 'Phone Number harus berupa angka',

        'note.max'                     => 'Note tidak boleh lebih dari 255 kata',
        
        

        ];
    }

}
