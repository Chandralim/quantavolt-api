<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PagResource extends JsonResource
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
            'project'           => new ProjectResource($this->whenLoaded('project')) ?? "",
            'project_no'        => $this->project_no ?? "",
            'need'              => $this->need ?? "",
            'date'              => $this->date,
            'part'              => $this->part ?? "",
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'creator'           => new UserResource($this->whenLoaded("creator")),
            'updator'           => new UserResource($this->whenLoaded("updator")),
            'pag_details'       => PagDetailResouce::collection($this->whenLoaded('pag_details')),
        ];
    }
}
