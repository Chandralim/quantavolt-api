<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PagDetailResouce extends JsonResource
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
            'pag_no'            => $this->pag_no,
            'qty'               => $this->qty,
            'qty_used'          => $this->qty_used,
            'note'              => $this->note,
            'item'              => new ItemResource($this->whenLoaded('item')),
            // 'unit'              => new UnitResource($this->whenLoaded('unit')),
        ];
    }
}
