<?php

namespace App\Http\Resources\Main;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberInstituteResource extends JsonResource
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
            'member_id' => $this->member_id,
            'institute_id' => $this->institute_id,
            'member' => new MemberResource($this->whenLoaded('member')),
            'institute' => new InstituteResource($this->whenLoaded('institute')),
            'role' => $this->role,
        ];
    }
}
