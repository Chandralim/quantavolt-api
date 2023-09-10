<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'            => $this->id,
            'username'      => $this->username,
            // 'name' => $this->name??'',
            'password'      => '',
            // 'goods_receipt' => new GoodsReceiptResource($this->whenLoaded('goods_receipt')),
            // 'ordinal'=>$this->ordinal,
            // 'material'=>new MaterialResource($this->whenLoaded('material')),
            // 'qty' => $this->qty,
            // 'goods_receipt_details'=>GoodsReceiptDetailResource::collection($this->whenLoaded('goods_receipt_details')),
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
            'employee'      => new EmployeeResource($this->whenLoaded('employee')),
            // 'role' => $this->role,
            // 'role_id' => $this->role_id,
            'can_login'     => $this->can_login ? 1 : 0,
        ];

    }
}
