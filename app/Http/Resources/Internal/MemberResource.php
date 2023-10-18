<?php

namespace App\Http\Resources\Internal;

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
            'phone_number'        => $this->phone_number ?? "",
            'fullname'            => $this->fullname ?? "",
            'password'            => "",
            // 'role'                => $this->role ?? '',
            'can_login'           => $this->can_login ? 1 : 0,
            // 'photo'               => $request->getHttpHost() . "/" . $this->photo ?? "",
            'photo'               => $this->photo ? '/api/' . $this->photo : "",

            'created_at'          => $this->created_at,
            'updated_at'          => $this->updated_at,
            'created_by'          => $this->created_by,
            'updated_by'          => $this->updated_by,
            'internal_created_at' => $this->internal_created_at,
            'internal_updated_at' => $this->internal_updated_at,
            'internal_created_by' => $this->internal_created_by,
            'internal_updated_by' => $this->internal_updated_by,
            'internal_updator' => new UserResource($this->whenLoaded('internal_updator')),
            'internal_creator' => new UserResource($this->whenLoaded('internal_creator')),
            'roles' => $this->whenPivotLoaded('member_institutes', function () {
                return $this->pivot->role;
            }),

        ];
    }
}
