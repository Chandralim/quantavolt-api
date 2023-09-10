<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PbgDetailResource extends JsonResource
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
            'pbg_no'    => $this->pbg_no,
            'qty'       => $this->qty,
            'note'      => $this->note ?? "",
            'item'      => new ItemResource($this->whenLoaded('item')),
        ];
    }
}
