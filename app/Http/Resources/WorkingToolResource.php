<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkingToolResource extends JsonResource
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
            'code'           => $this->code,
            'name'           => $this->name,
            'unit'           => new UnitResource($this->whenLoaded(('unit'))),
            'specification'  => $this->specification,
            'creator'        => new UserResource($this->whenLoaded("creator")),
            'updator'        => new UserResource($this->whenLoaded("updator")),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
