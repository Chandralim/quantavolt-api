<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PbgResource extends JsonResource
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
            'pag'               => new PagResource($this->whenLoaded('pag')),
            'pag_no'            => $this->pag_no,
            'date'              => $this->date,
            'updated_at'        => $this->updated_at,
            'creator'           => new UserResource($this->whenLoaded("creator")),
            'updator'           => new UserResource($this->whenLoaded("updator")),
            'pbg_details'       => PbgDetailResource::collection($this->whenLoaded('pbg_details')),
        ];
    }
}
