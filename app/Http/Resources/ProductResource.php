<?php

namespace App\Http\Resources\Internal;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'pom_code' => $this->pom_code,
            'price_distributor' => $this->price_distributor,
            'price_consumer' => $this->price_consumer,
            'point' => $this->point,
            'utility' => $this->utility,
            'how_to_use' => $this->how_to_use,
            'dosage' => $this->dosage,
            'how_to_save' => $this->how_to_save,
            'warning_and_attention' => $this->warning_and_attention,
            'packaging_unit' => $this->packaging_unit,
            'unit_of_content' => $this->unit_of_content,
            'net_weight_each_content' => $this->net_weight_each_content,
            'net_weight_unit' => $this->net_weight_unit,
            'package_contents' => $this->package_contents,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];;

    }
}
