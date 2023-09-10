<?php
namespace App\Http\Requests\Internal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class BankRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [];
        if (request()->isMethod('post')) {
            $rules['code'] = 'required|min:3|max:255|regex:/^\S*$/|unique:\App\Model\Bank,code';
        }
        if (request()->isMethod('get')) {
            $rules['code'] = 'required|min:3|max:255|regex:/^\S*$/|exists:\App\Model\Bank,code';
        }
        if (request()->isMethod('put')) {
            $rules['code_old'] = 'required|min:3|max:255|regex:/^\S*$/|exists:App\Model\Bank,code';
            $rules['code'] = 'required|min:3|max:255|regex:/^\S*$/|unique:\App\Model\Bank,code,'.request()->code_old;
        }
        if(request()->isMethod('post') || request()->isMethod('put')){
            $rules['name'] = 'required|max:255';
            $rules['account_number'] = 'required|max:255';            
            $rules['description'] = 'sometimes|max:255';            
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'code_old.required' => 'Kode Lama tidak boleh kosong',
            'code_old.min' => 'Kode Lama tidak boleh kurang dari 8 karakter',
            'code_old.max' => 'Kode Lama tidak boleh lebih dari 255 karakter',
            'code_old.regex' => 'Kode Lama tidak boleh ada spasi',
            // 'code_old.unique' => 'Kode Lama sudah digunakan',
            'code_old.exists' => 'Kode Lama tidak terdaftar',

            'code.required' => 'Kode tidak boleh kosong',
            'code.min' => 'Kode tidak boleh kurang dari 8 karakter',
            'code.max' => 'Kode tidak boleh lebih dari 255 karakter',
            'code.regex' => 'Kode tidak boleh ada spasi',
            'code.unique' => 'Kode sudah digunakan',
            'code.exists' => 'Kode tidak terdaftar',
            
            'name.required' => 'Nama tidak boleh kosong',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter',

            'account_number.required' => 'No Rekening tidak boleh kosong',
            'account_number.max' => 'No Rekening tidak boleh lebih dari 255 karakter',

            'description.required' => 'Keterangan tidak boleh kosong',
            'description.max' => 'Keterangan tidak boleh lebih dari 255 karakter',
        ];
    }
}
