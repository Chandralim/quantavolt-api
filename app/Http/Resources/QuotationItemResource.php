<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\MyLib;
class QuotationItemResource extends JsonResource
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

        $result = [
            'code'           => $this->code,
            'name'          => $this->name,
            'brand'          => $this->brand ?? "",
            'size'           => $this->size ?? "", 
            'model'          => $this->model ?? "",
            'type'           => $this->type ?? "",
            'unit'           => new UnitResource($this->whenLoaded('unit')),
            'creator'        => new UserResource($this->whenLoaded("creator")),
            'updator'        => new UserResource($this->whenLoaded("updator")),
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
            'selling_price'  => $this->selling_price,
        ];

        $auth = MyLib::user();
        if(!MyLib::checkDataScope($auth, ['dp-quotation_item-except-purchase_price']))
        $result ['purchase_price'] =  $this->purchase_price ?? 0;

        if(!MyLib::checkDataScope($auth, ['dp-quotation_item-except-shipping_cost']))
        $result ['shipping_cost'] =  $this->shipping_cost ?? 0;

        if(!MyLib::checkDataScope($auth, ['dp-quotation_item-except-percent']))
        $result ['percent'] =  $this->percent ?? 0;

        return $result;

    }
}
