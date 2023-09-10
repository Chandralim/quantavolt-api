<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\MyLib;
class QuotationDetailResource extends JsonResource
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
            'quotation_no'           => $this->quotation_no,
            // 'quotation_item_code'    => $this->quotation_item_code,
            'qty'                    => $this->qty, 
            'ordinal'                => $this->ordinal,
            'selling_price'          => $this->selling_price,
            'quotation_item'         => new QuotationItemResource($this->whenLoaded('quotation_item')),
        ];

        // $auth = MyLib::user();
        // if(!MyLib::checkDataScope($auth, ['dp-quotation_item-except-purchase_price']))
        // $result ['purchase_price'] =  $this->purchase_price;

        // if(!MyLib::checkDataScope($auth, ['dp-quotation_item-except-shipping_cost']))
        // $result ['shipping_cost'] =  $this->shipping_cost;

        // if(!MyLib::checkDataScope($auth, ['dp-quotation_item-except-percent']))
        // $result ['percent'] =  $this->percent;

        return $result;

    }
}
