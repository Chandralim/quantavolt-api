<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            // $rules['no']            = 'required|unique:App\Model\Project,no';
        }
        if(request()->isMethod('get'))
        {
            $rules['no'] = 'required|exists:App\Model\Project,no';
        }
        if(request()->isMethod('put'))
        {
            $rules['no'] = 'required|exists:App\Model\Project,no';
        }

        if(request()->isMethod('post') || request()->isMethod('put')){
            $rules['title']         = 'required';
            $rules['location']      = 'required';
            $rules['customer_code'] = 'required|exists:App\Model\Customer,code';
            $rules['type']          = 'required|in:Jasa,Material,Jasa & Material';
            $rules['date_start']    = 'required|date_format:Y-m-d';
            $rules['date_finish']   = 'nullable|date_format:Y-m-d';
            $rules['status']        = 'required|in:Draft,Sedang Diproses,Selesai,Batal';
            $rules['note']          = 'nullable|max:255';
        }

        return $rules;
    }
}
