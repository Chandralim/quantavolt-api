<?php
namespace App\Http\Requests\Internal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class ProductRequest extends FormRequest
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
        if (request()->isMethod('get')) {
            $rules['code'] = 'required|exists:App\Model\Product,code';
        }
        if (request()->isMethod('post')) {
            $rules['code'] = 'required|unique:App\Model\Product,code';
            $rules['name'] = 'required|unique:App\Model\Product,name';
            $rules['pom_code'] = 'required|unique:App\Model\Product,pom_code';
        }
        if (request()->isMethod('put')) {
            $rules['code_old'] = 'required|exists:App\Model\Product,code';
            $rules['code'] = 'required|unique:App\Model\Product,code,'.request()->code_old;
            $rules['name'] = 'required|unique:App\Model\Product,name,'.request()->code_old;
            $rules['pom_code'] = 'required|unique:App\Model\Product,pom_code,'.request()->code_old;
        }
        if (request()->isMethod('post') || request()->isMethod('put')) {
            $rules['price_distributor'] = 'required|numeric';
            $rules['price_consumer'] = 'required|numeric';
            $rules['point'] = 'required|numeric';
            $rules['utility'] = 'required';
            $rules['how_to_use'] = 'required';
            $rules['dosage'] = 'required';
            $rules['how_to_save'] = 'required';
            $rules['warning_and_attention'] = 'required';
            $rules['packaging_unit'] = 'required';
            $rules['unit_of_content'] = 'required';
            $rules['net_weight_each_content'] = 'required|numeric';
            $rules['net_weight_unit'] = 'required';
            $rules['package_contents'] = 'required|numeric';
            
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'code_old.required' => 'Kode Produk Lama tidak boleh kosong',
            'code_old.exists' => 'Kode Produk Lama tidak terdaftar',
            'code_old.unique' => 'Kode Produk Lama telah terdaftar',

            'code.required' => 'Kode Produk tidak boleh kosong',
            'code.exists' => 'Kode Produk tidak terdaftar',
            'code.unique' => 'Kode Produk telah terdaftar',
            
            'name.required' => 'Nama tidak boleh kosong',
            'name.unique' => 'Nama sudah digunakan',

            'pom_code.required' => 'No POM tidak boleh kosong',
            'pom_code.unique' => 'No POM telah terdaftar',


            'price_distributor.required' => 'Harga Distributor tidak boleh kosong',
            'price_distributor.numeric' => 'Harga Distributor harus berupa angka',

            'price_consumer.required' => 'Harga Konsumen tidak boleh kosong',
            'price_consumer.numeric' => 'Harga Konsumen harus berupa angka',

            'point.required' => 'Point tidak boleh kosong',
            'point.numeric' => 'Point harus berupa angka',

            'utility.required' => 'Kegunaan tidak boleh kosong',

            'how_to_use.required' => 'Cara Pemakaian tidak boleh kosong',

            'dosage.required' => 'Dosis tidak boleh kosong',

            'how_to_save.required' => 'Cara Menyimpan tidak boleh kosong',

            'warning_and_attention.required' => 'Peringatan dan perhatian tidak boleh kosong',

            'packaging_unit.required' => 'Satuan Kemasan tidak boleh kosong',
            'unit_of_content.required' => 'Satuan Isi tidak boleh kosong',

            'net_weight_each_content.required' => 'Berat Bersih Setiap Isi tidak boleh kosong',
            'net_weight_each_content.numeric' => 'Berat Bersih Setiap Isi harus berupa angka',

            'net_weight_unit.required' => 'Satuan Berat Bersih tidak boleh kosong',

            'package_contents.required' => 'Isi Kemasan tidak boleh kosong',
            'package_contents.numeric' => 'Isi Kemasan harus berupa angka',
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
