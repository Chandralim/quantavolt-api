<?php

namespace App\Http\Resources\Main;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomResource extends JsonResource
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
            'id'                  => $this->id,
            'name'                => $this->name ?? "",
            'homeroom_teacher_id' => $this->homeroom_teacher_id ?? "",
            'homeroom_teacher'    => new MemberResource($this->whenLoaded('homeroom_teacher')),

            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
            'created_by'          => $this->created_by,
            'updated_by'          => $this->updated_by,
        ];
    }
}
