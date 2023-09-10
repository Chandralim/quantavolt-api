<?php
namespace App\Http\Requests\Internal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ProductCategoryRequest extends FormRequest
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
            $rules['name'] = 'required|unique:App\Model\ProductCategory,name';
            //    $rules['code'] = 'required|min:3|regex:/^\S*$/|unique:\App\Model\Cash,code';
        }
        if (request()->isMethod('get')) {
            $rules['id'] = 'required|exists:App\Model\ProductCategory,id';
            //    $rules['code'] = 'required|min:3|regex:/^\S*$/|unique:\App\Model\Cash,code';
        }
        if (request()->isMethod('put')) {
            $rules['id'] = 'required|exists:App\Model\ProductCategory,id';
            $rules['name'] = 'required|unique:App\Model\ProductCategory,name,'.request()->id;
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'id.required' => 'ID tidak boleh kosong',
            'id.exists' => 'ID tidak terdaftar',
            
            'name.required' => 'Nama tidak boleh kosong',
            'name.unique' => 'Nama sudah digunakan',

            //    'code.regex' => 'Kode tidak boleh ada spasi',
        ];
    }

    // public $validator = null;
    // protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    // {
    //   return "failed";
    //     $this->validator = $validator;
    // }

}
