<?php
namespace App\Http\Requests\Internal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class PermissionRequest extends FormRequest
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
            $rules['name'] = 'required|regex:/^\S*$/|max:255|unique:App\Model\Internal\Permission,name';
        }
        if (request()->isMethod('get')) {
            $rules['id'] = 'required|exists:App\Model\Internal\Permission,id';
        }
        if (request()->isMethod('put')) {
            $rules['id'] = 'required|exists:App\Model\Internal\Permission,id';
            $rules['name'] = 'required|regex:/^\S*$/|max:255|unique:App\Model\Internal\Permission,name,'.request()->id;
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'id.required' => 'ID tidak boleh kosong',
            'id.exists' => 'ID tidak terdaftar',
            
            'name.regex' => 'Nama tidak boleh ada spasi',
            'name.required' => 'Nama tidak boleh kosong',
            'name.unique' => 'Nama sudah digunakan',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => strtolower($this->name),
        ]);
    }

    // public $validator = null;
    // protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    // {
    //   return "failed";
    //     $this->validator = $validator;
    // }

}
