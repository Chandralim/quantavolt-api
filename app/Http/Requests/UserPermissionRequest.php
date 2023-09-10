<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UserPermissionRequest extends FormRequest
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
        // if (request()->isMethod('post')) {
        //     $rules['username'] = 'required|regex:/^\S*$/|max:255|unique:App\Model\User,username';
        //     //    $rules['code'] = 'required|min:3|regex:/^\S*$/|unique:\App\Model\Cash,code';
        //     $rules['password'] = 'required|min:8|max:255';
        // }
        if (request()->isMethod('get')) {
            $rules['id'] = 'required|exists:App\Model\User,id';
            //    $rules['code'] = 'required|min:3|regex:/^\S*$/|unique:\App\Model\Cash,code';
        }
        if (request()->isMethod('put')) {
            $rules['id'] = 'required|exists:App\Model\User,id';
            // $rules['username'] = 'required|regex:/^\S*$/|max:255|unique:App\Model\User,username,'.request()->id;
            // $rules['password'] = 'nullable|min:8|max:255';
        }
        if(request()->isMethod('post') || request()->isMethod('put')){
            // $rules['employee_nik'] = 'nullable|exists:App\Model\Employee,nik';
            // // $rules['name'] = 'required|max:255';
            // $rules['can_login'] = 'required|in:0,1';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'id.required' => 'ID tidak boleh kosong',
            'id.exists' => 'ID tidak terdaftar',
            
            // 'username.regex' => 'Nama Pengguna tidak boleh ada spasi',
            // 'username.required' => 'Nama Pengguna tidak boleh kosong',
            // 'username.unique' => 'Nama Pengguna sudah digunakan',
            // 'username.max' => 'Nama Pengguna tidak boleh lebih dari 255 karakter',

            // 'password.required' => 'Kata Sandi tidak boleh kosong',
            // 'password.min' => 'Kata Sandi tidak boleh kurang dari 8 karakter',
            // 'password.max' => 'Kata Sandi tidak boleh lebih dari 255 karakter',

            // 'role_id.required' => 'Jabatan tidak boleh kosong',
            // 'role_id.exists' => 'Role tidak terdaftar',

            // 'name.required' => 'Nama Identitas tidak boleh kosong',
            // 'name.max' => 'Nama Identitas tidak boleh lebih dari 255 karakter',

            // 'can_login.required' => 'Dapat Masuk tidak boleh kosong',
            // 'can_login.in' => 'Dapat Masuk harus di pilih',
            // //    'code.regex' => 'Kode tidak boleh ada spasi',

            // 'employee_nik.exists' => 'NIK tidak terdaftar',

        ];
    }

    // https://www.codecheef.org/article/sanitize-form-request-data-before-validation-in-laravel
    protected function prepareForValidation()
    {
        $this->merge([
            'username' => strtolower($this->username),

            // 'title' => fix_typos($this->title),
            // 'body' => filter_malicious_content($this->body),
            // 'tags' => convert_comma_separated_values_to_array($this->tags),
            // 'is_published' => (bool) $this->is_published,
        ]);
    }

    // public $validator = null;
    // protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    // {
    //   return "failed";
    //     $this->validator = $validator;
    // }

}
