<?php

namespace App\Http\Resources\Main;

use Illuminate\Http\Resources\Json\JsonResource;

class InstituteResource extends JsonResource
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
            'id'            => $this->id,
            'name'      => $this->name,
            'address'      => $this->address,
            'contact_number'      => $this->contact_number,
            'contact_person'      => $this->contact_person,
            'active_until' => $this->active_until ?? '',
            'internal_marketer_by' => $this->internal_marketer_by,
            'created_at' => $this->created_at,
            'internal_created_by' => $this->internal_created_by,
            'updated_at' => $this->updated_at,
            'internal_updated_by' => $this->internal_updated_by,
        ];
    }
}
