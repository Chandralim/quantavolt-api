<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return[
            'code'           => $this->code,
            'name'           => $this->name,
            'unit'           => new UnitResource($this->whenLoaded('unit')),
            // 'brand'          => $this->brand,
            // 'model'          => $this->model,
            // 'type'           => $this->type,
            // 'size'           => $this->size,
            // 'color'          => $this->color,
            'stock_min'      => $this->stock_min,
            'description'    => $this->description,
            // 'capital_price'  => $this->capital_price,
            'creator'        => new UserResource($this->whenLoaded("creator")),
            'updator'        => new UserResource($this->whenLoaded("updator")),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
