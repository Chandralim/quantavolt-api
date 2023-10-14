<?php

namespace App\Http\Requests\Main;

use App\Helpers\MyLib;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassRoomRequest extends FormRequest
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
            // $rules['name'] = 'required|max:255|unique:App\Model\Main\Institute,name';
        }
        if (request()->isMethod('get')) {
            $rules['id'] = 'required|exists:App\Model\Main\ClassRoom,id';
        }
        if (request()->isMethod('put')) {
            $rules['id'] = 'required|exists:App\Model\Main\ClassRoom,id';
            // $rules['name'] = 'required|max:255|unique:App\Model\Main\Institute,name,' . request()->id;
        }
        if (request()->isMethod('post') || request()->isMethod('put')) {
            // $rules['internal_marketer_id'] = 'required|exists:App\Model\Internal\User,id';
            // $rules['operator_member_id'] = 'nullable|exists:App\Model\Main\Member,id';
            $rules['name'] = 'required';
            // $rules['contact_number'] = 'required|max:20';
            $rules['homeroom_teacher_id'] = 'required|exists:App\Model\Main\member,id';
            $rules['link_name'] = 'required|exists:App\Model\Main\Institute,link_name';
            // $rules['role'] = 'required|in:operator,teacher,student';
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'id.required' => 'ID tidak boleh kosong',
            'id.exists' => 'ID tidak terdaftar',

            'name.required' => 'Nama Kelas tidak boleh kosong',


            'link_name.required' => 'Nama link tidak boleh kosong',
            'link_name.exists' => 'Nama link tidak terdaftar',

            'homeroom_teacher_id.required' => 'Wali Kelas tidak boleh kosong',
            'homeroom_teacher_id.exists' => 'Wali Kelas tidak terdaftar',

            // 'role.required' => 'Role tidak boleh kosong',
            // 'role.in' => 'Role harus sesuai dengan ketentuan',

        ];
    }
}
