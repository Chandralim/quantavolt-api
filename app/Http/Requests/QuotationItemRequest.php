<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\MyLib;

class QuotationItemRequest extends FormRequest
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
            $rules['code']           = 'required|regex:/^\S*$/|unique:App\Model\QuotationItem,code';
        }
        if (request()->isMethod('get')) {
            $rules['code']           = 'required|exists:App\Model\QuotationItem,code';
        }
        if (request()->isMethod('put')) {
            $rules['code_old']       = 'required|regex:/^\S*$/|exists:App\Model\QuotationItem,code';
            $rules['code']           = 'required|regex:/^\S*$/|unique:App\Model\QuotationItem,code,'.request()->code_old;
        }

        if(request()->isMethod('post') || request()->isMethod('put')){
            $rules['unit_code']      = 'required|exists:App\Model\Unit,code';
            $rules['name']          = 'required';
            // $rules['brand']          = 'required';
            $rules['size']           = 'nullable|between:0,99.99';
            // $rules['model']          = 'required';
            // $rules['type']           = 'required';
            
            $auth = MyLib::user();
            if(!MyLib::checkDataScope($auth, ['dp-quotation_item-hide-purchase_price']))
            $rules['purchase_price'] = 'nullable|numeric';
            
            if(!MyLib::checkDataScope($auth, ['dp-quotation_item-hide-shipping_cost']))
            $rules['shipping_cost']  = 'nullable|numeric';

            if(!MyLib::checkDataScope($auth, ['dp-quotation_item-hide-percent']))
            $rules['percent']        = 'nullable';

        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'code.required'           => 'Code tidak boleh kosong',
            'code.regex'              => 'Code tidak boleh ada spasi',
            'code.unique'             => 'Code sudah terdaftar',
            
            'name.required'          => 'Nama tidak boleh kosong',

            'brand.required'          => 'Brand tidak boleh kosong',
            
            'size.required'           => 'Size tidak boleh kosong',
            // 'size.numeric'            => 'Size harus berupa angaka',

            'model.required'          => 'Model wajib di isi',
            'type.required'           => 'Type tidak boleh kosong',
            'unit_code.required'      => 'Unit code tidak boleh kosong',
            'purchase_price.required' => 'Purchase prize tidak bole kosong',
            'purchase_price.numeric'  => 'Purchase prize harus berupa angka',
            'shipping_cost.required'  => 'Shipping cost tidak bole kosong',
            'shipping_cost.numeric'   => 'Shipping cost harus berupa angka',
            'percent.required'        => 'Purchase wajib diisi',

        ];
    }
}
