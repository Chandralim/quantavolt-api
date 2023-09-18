<?php
namespace App\Http\Requests\Internal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UserRequest extends FormRequest
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
            $rules['email'] = 'required|email|regex:/^\S*$/|max:255|unique:App\Model\Internal\User,email';
            //    $rules['code'] = 'required|min:3|regex:/^\S*$/|unique:\App\Model\Cash,code';
            $rules['password'] = 'required|min:8|max:255';
        }
        if (request()->isMethod('get')) {
            $rules['id'] = 'required|exists:App\Model\Internal\User,id';
            //    $rules['code'] = 'required|min:3|regex:/^\S*$/|unique:\App\Model\Cash,code';
        }
        if (request()->isMethod('put')) {
            $rules['id'] = 'required|exists:App\Model\Internal\User,id';
            $rules['email'] = 'required|email|regex:/^\S*$/|max:255|unique:App\Model\Internal\User,email,'.request()->id;
            $rules['password'] = 'nullable|min:8|max:255';
        }
        if(request()->isMethod('post') || request()->isMethod('put')){
            // $rules['employee_no'] = 'nullable|exists:App\Model\Employee,no';
            // $rules['name'] = 'required|max:255';
            $rules['can_login'] = 'required|in:0,1';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'id.required' => 'ID tidak boleh kosong',
            'id.exists' => 'ID tidak terdaftar',
            
            'email.regex' => 'Email tidak boleh ada spasi',
            'email.required' => 'Email tidak boleh kosong',
            'email.unique' => 'Email sudah digunakan',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter',
            'email.email' => 'Format email salah',

            'password.required' => 'Kata Sandi tidak boleh kosong',
            'password.min' => 'Kata Sandi tidak boleh kurang dari 8 karakter',
            'password.max' => 'Kata Sandi tidak boleh lebih dari 255 karakter',

            // 'role_id.required' => 'Jabatan tidak boleh kosong',
            // 'role_id.exists' => 'Role tidak terdaftar',

            // 'name.required' => 'Nama Identitas tidak boleh kosong',
            // 'name.max' => 'Nama Identitas tidak boleh lebih dari 255 karakter',

            'can_login.required' => 'Dapat Masuk tidak boleh kosong',
            'can_login.in' => 'Dapat Masuk harus di pilih',
            //    'code.regex' => 'Kode tidak boleh ada spasi',

            // 'employee_no.exists' => 'Nomor Karyawan tidak terdaftar',

        ];
    }

    // https://www.codecheef.org/article/sanitize-form-request-data-before-validation-in-laravel
    // protected function prepareForValidation()
    // {
    //     $this->merge([
    //         'email' => strtolower($this->email),

    //         // 'title' => fix_typos($this->title),
    //         // 'body' => filter_malicious_content($this->body),
    //         // 'tags' => convert_comma_separated_values_to_array($this->tags),
    //         // 'is_published' => (bool) $this->is_published,
    //     ]);
    // }

    // public $validator = null;
    // protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    // {
    //   return "failed";
    //     $this->validator = $validator;
    // }

}
