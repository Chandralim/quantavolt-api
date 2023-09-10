<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkingToolRequest extends FormRequest
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
            $rules['code']          = 'required|regex:/^\S*$/|unique:App\Model\WorkingTool,code';
            $rules['name']          = 'required';
            $rules['unit_code']     = 'required|exists:App\Model\Unit,code';
            $rules['specification'] = 'required';
        }
        if(request()->isMethod('get'))
        {
            $rules['code']           = 'required|exists:App\Model\WorkingTool,code';
        }
        if(request()->isMethod('put'))
        {
            $rules['code_old']       = 'required|regex:/^\S*$/|exists:App\Model\WorkingTool,code';
            $rules['code']           = 'required|regex:/^\S*$/|unique:App\Model\WorkingTool,code,'.request()->code_old;
            $rules['name']           = 'required';
            $rules['unit_code']      = 'required|exists:App\Model\Unit,code';
            $rules['specification']  = 'required';
        }

        return $rules;
    }
}
