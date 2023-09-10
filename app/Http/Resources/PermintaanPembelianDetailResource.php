<?php

namespace App\Http\Resources\Internal;

use Illuminate\Http\Resources\Json\JsonResource;

class PermintaanPembelianDetailResource extends JsonResource
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
            'ordinal' => $this->ordinal,
            'name' => $this->name??'',
            'qty' => $this->qty,
            'unit' => $this->unit,
            'price' => $this->price,
            'supplier_name' => $this->supplier_name??'',
            'note' => $this->note??'',
            'reject_note' => $this->reject_note??'',
            // 'checked' => $this->checked,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator'=>new AdminOutResource($this->whenLoaded('creator')),
            'updator'=>new AdminOutResource($this->whenLoaded('updator')),

            // 'permintaan_pembelian_details'=>PermintaanPembelianDetailResource::collection($this->whenLoaded('permintaan_pembelian_details')),
        ];

    }
}
