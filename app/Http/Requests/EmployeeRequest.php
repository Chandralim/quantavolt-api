<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class EmployeeRequest extends FormRequest
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
            $rules['no']                = 'required|integer|regex:/^\S*$/|unique:App\Model\Employee,no';
        }
        if (request()->isMethod('get')) {
            $rules['no']                = 'required|exists:App\Model\Employee,no';
        }
        if (request()->isMethod('put')) {
            $rules['no_old']            = 'required|regex:/^\S*$/|exists:App\Model\Employee,no';
            $rules['no']                = 'required|regex:/^\S*$/|unique:App\Model\Employee,no,'.request()->no_old;
        }
        if(request()->isMethod('post') || request()->isMethod('put')){
            $rules['nik']               = 'required|integer';
            $rules['fullname']          = 'required|regex:/^[a-zA-Z- ]+$/|max:255';
            $rules['birth_date']        = 'required|date_format:Y-m-d';
            $rules['address']           = 'required';
            $rules['handphone_number']  = 'required|numeric|digits_between:10,15';
            $rules['work_start_date']   = 'required|date_format:Y-m-d';
            $rules['work_stop_date']    = 'nullable|date_format:Y-m-d';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'no.required'      => 'Nomor Karyawan tidak boleh kosong',
            'no.integer'       => 'Nomor Karyawan harus berupa angka',
            'no.exists'        => 'Nomor Karyawan tidak terdaftar',
            'no.unique'        => 'Nomor Karyawan sudah terdaftar',
            'no.regex'         => 'Nomor Karyawan tidak boleh ada spasi',
            
            'nik.required'      => 'NIK tidak boleh kosong',
            'nik.integer'       => 'NIK harus berupa angka',
            
            'fullname.required' => 'Nama Lengkap tidak boleh kosong',
            'fullname.max'      => 'Nama Lengkap tidak boleh lebih dari 255 karakter',
            'fullname.regex'    => 'Nama hanya boleh huruf',

            'birth_date.required'       => 'Tanggal Lahir tidak boleh kosong',
            'birth_date.date_format'    => 'Format Tanggal Lahir salah',

            'address.required'          => 'Alamat tidak boleh kosong',

            'handphone_number.required'         => 'No HP tidak boleh kosong',
            'handphone_number.digits_between'   => 'No HP harus sebanyak 10 hingga 15 digit',
            'handphone_number.numeric'          => 'No HP hanya boleh angka',

            'work_start_date.required'          => 'Tanggal Mulai Kerja tidak boleh kosong',
            'work_start_date.date_format'       => 'Format Tanggal Mulai Kerja salah',
            
            'work_stop_date.date_format'        => 'Format Tanggal Berhenti Kerja salah',
        ];
    }

    // https://www.codecheef.org/article/sanitize-form-request-data-before-validation-in-laravel
    // protected function prepareForValidation()
    // {
    //     // $this->merge([
    //     //     'username' => strtolower($this->username),

    //     //     // 'title' => fix_typos($this->title),
    //     //     // 'body' => filter_malicious_content($this->body),
    //     //     // 'tags' => convert_comma_separated_values_to_array($this->tags),
    //     //     // 'is_published' => (bool) $this->is_published,
    //     // ]);
    // }

    // public $validator = null;
    // protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    // {
    //   return "failed";
    //     $this->validator = $validator;
    // }

}
