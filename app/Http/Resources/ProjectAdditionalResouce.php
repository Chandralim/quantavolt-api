<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectAdditionalResouce extends JsonResource
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
            'project_no'        => new ProjectResource($this->whenLoaded('project')),
            'ordinal'           => $this->ordinal,
            'name'              => $this->name,
            'qty_assumption'    => $this->qty_assumption,
            'qty_realization'   => $this->qty_realization ?? "",
            'unit'              => new UnitResource($this->whenLoaded('unit')),
            'price_assumption'  => $this->price_assumption ?? "",
            'price_realization' => $this->price_realization ?? "",
            'note'              => $this->note,
            'is_locked'         => $this->is_locked,
            'creator'                => new UserResource($this->whenLoaded("creator")),
            'updator'                => new UserResource($this->whenLoaded("updator")),
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
}
