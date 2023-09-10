<?php

namespace App\Http\Resources\Internal;

use Illuminate\Http\Resources\Json\JsonResource;

class BankResource extends JsonResource
{
    public $preserveKeys = true;

    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'account_number' => $this->account_number,
            'description' => $this->description??'',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
