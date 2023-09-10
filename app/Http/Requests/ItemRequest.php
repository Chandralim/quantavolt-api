<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            $rules['code_old']       = 'required|regex:/^[0-9]{3}\.[0-9]{2}\.[0-9]{3}$/|exists:App\Model\Item,code';
            $rules['code']           = 'required|regex:/^[0-9]{3}\.[0-9]{2}\.[0-9]{3}$/|unique:App\Model\Item,code,'.request()->code_old;
        }
        if(request()->isMethod('get'))
        {
            $rules['code']           = 'required|exists:App\Model\Item,code';
        }
        if(request()->isMethod('post') || request()->isMethod('put'))
        {
            $rules['code']           = 'required|regex:/^[0-9]{3}\.[0-9]{2}\.[0-9]{3}$/|unique:App\Model\Item,code,'.request()->code_old;
            $rules['name']           = 'required';
            $rules['unit_code']      = 'required|exists:App\Model\Unit,code';
            // $rules['brand']          = 'required';
            // $rules['model']          = 'required';
            // $rules['type']           = 'required';
            // $rules['size']           = 'required|between:0,99.99';
            // $rules['color']          = 'required';
            $rules['stock_min']      = 'required';
            // $rules['capital_price']  = 'required';
        }

        return $rules;

    }

    public function messages()
    {
        return [
            'code.required'           => 'Kode tidak boleh kosong',
            'code.regex'              => 'Kode Tidak sesuai format',
            'code.unique'             => 'Kode sudah terdaftar',
            

            'name.required'           => 'Nama tidak boleh kosong',
            'unit_code.required'      => 'Unit code tidak boleh kosong',
            // 'brand.required'          => 'Brand tidak boleh kosong',
            // 'model.required'          => 'Model tidak boleh kososng',
            // 'type.required'           => 'Type tidak boleh kosong',
            // 'size.required'           => 'Size tidak boleh kosong',
            // 'color.required'          => 'Color tidak boleh kosong',
            'stock_min.required'      => 'Stok min tidak boleh kosong',
            'description.required'    => 'Deskripsi tidak boleh kosong',
            // 'capital_price.required'    => 'Deskripsi tidak boleh kosong',
        ];
    }


}
