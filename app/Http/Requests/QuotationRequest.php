<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuotationRequest extends FormRequest
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
            // $rules['no'] = 'required|unique:App\Model\Quotation,no';
        }
        if(request()->isMethod('get'))
        {
            $rules['no'] = 'required|exists:App\Model\Quotation,no';
        }
        if(request()->isMethod('put'))
        {
            $rules['no'] = 'required|exists:App\Model\Quotation,no';

            // $rules['no_old'] = 'required|exists:App\Model\Quotation,no';
            // $rules['no']     = 'required|unique:App\Model\Quotation,no,'.request()->no_old;
        }

        return $rules;

    }

    public function messages()
    {
        return [
            'no.required'   => 'Nomor Quotation Wajib Diisi',
            'no.unique'     => 'Nomor Quotation Sudah Dipakai',
            'no.exists'     => 'Nomor Quotation tidak terdaftar'
        ];
    }

}
