<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'code'         => $this->code,
            'name'         => $this->name,
            'address'      => $this->address,
            'phone_number' => $this->phone_number,
            'hp_number'    => $this->hp_number,
            'note'         => $this->note,
            "creator"      => new UserResource($this->whenLoaded("creator")),
            'created_at'   => $this->created_at,
            'updator'      => new UserResource($this->whenLoaded("updator")),
            'updated_at'   => $this->updated_at, 
        ];
    }
}
