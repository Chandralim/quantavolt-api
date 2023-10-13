<?php

namespace App\Http\Resources\Main;

use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'username'            => $this->username ?? "",
            'email'               => $this->email ?? "",
            'fullname'            => $this->fullname ?? "",
            'can_login'           => $this->can_login ? 1 : 0,
            // 'photo'               => $request->getHttpHost() . "/" . $this->photo ?? "",
            'photo'               => $this->photo ? '/api/' . $this->photo : "",

            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
            'created_by'          => $this->created_by,
            'updated_by'          => $this->updated_by,

        ];
    }
}
