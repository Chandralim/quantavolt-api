<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'code'       => $this->code,
            'name'       => $this->name,
            "creator"    => new UserResource($this->whenLoaded("creator")),
            'created_at' => $this->created_at,
            'updator'    => new UserResource($this->whenLoaded("updator")),
            'updated_at' => $this->updated_at, 
        ];
    }
}
