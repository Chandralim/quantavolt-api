<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbgRequest extends FormRequest
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
            // $rules['no'] = 'required|unique:App\Model\Quotation,no';
        }
        if (request()->isMethod('get')) {
            $rules['no'] = 'required|exists:App\Model\Pbg,no';
        }
        if (request()->isMethod('put')) {
            $rules['no'] = 'required|exists:App\Model\Pbg,no';
        }
        if (request()->isMethod('post') || request()->isMethod('put')) {
            // $rules['no']            = 'required|exists:App\Model\Pag,no';
            $rules['pag_no']        = 'nullable|exists:App\Model\Pag,no';
            $rules['date']          = 'required|date_format:Y-m-d';
        }

        return $rules;
    }
}
