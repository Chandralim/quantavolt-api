<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuotationResource extends JsonResource
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
            'no'                => $this->no,
            'quotation_details' => QuotationDetailResource::collection($this->whenLoaded("quotation_details")),
            'creator'           => new UserResource($this->whenLoaded("creator")),
            'updator'           => new UserResource($this->whenLoaded("updator")),
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at, 
        ];
    }
}
