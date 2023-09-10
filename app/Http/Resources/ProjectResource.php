<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'no'                     => $this->no,
            'title'                  => $this->title,
            'date'                   => $this->date,
            'project_materials'      => ProjectMaterialResource::collection($this->whenLoaded('project_materials')),
            'project_workers'        => ProjectWorkerResource::collection($this->whenLoaded('project_workers')),
            'project_working_tools'  => ProjectWorkingToolResource::collection($this->whenLoaded('project_working_tools')),
            'project_additionals'    => ProjectAdditionalResouce::collection($this->whenLoaded('project_additionals')),
            'location'               => $this->location ?? "",
            'customer'               => new CustomerResource($this->whenLoaded('customer')),
            'type'                   => $this->type,
            'date_start'             => $this->date_start,
            'date_finis'             => $this->date_finish,
            'status'                 => $this->status,
            'note'                   => $this->note ?? "",
            'creator'                => new UserResource($this->whenLoaded("creator")),
            'updator'                => new UserResource($this->whenLoaded("updator")),
            'created_at'             => $this->created_at,
            'updated_at'             => $this->updated_at,
        ];
    }
}
