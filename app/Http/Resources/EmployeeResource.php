<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\MyLib;
class EmployeeResource extends JsonResource
{
    public $preserveKeys = true;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'no'                    => $this->no,
            'nik'                   => $this->nik,
            'fullname'              => $this->fullname,
            'birth_date'            => $this->birth_date ? MyLib::manualMillis($this->birth_date) : "",
            // 'birth_date' => $this->birth_date,
            'address'               => $this->address,
            'handphone_number'      => $this->handphone_number,
            'work_start_date'       => $this->work_start_date ? MyLib::manualMillis($this->work_start_date) : "",
            'work_stop_date'        => $this->work_stop_date ? MyLib::manualMillis($this->work_stop_date) : "",
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];

    }
}
