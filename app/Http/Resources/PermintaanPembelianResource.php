<?php

namespace App\Http\Resources\Internal;

use Illuminate\Http\Resources\Json\JsonResource;

class PermintaanPembelianResource extends JsonResource
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
            'no' => $this->no ?? '',
            'note' => $this->note ??'',

            'submit_by'=>$this->submit_by ?? '',
            'submit_at'=>$this->submit_at ?? '',
            'submitter'=>$this->submitter ? new AdminOutResource($this->submitter) : '',

            'check_by'=>$this->check_by ?? '',
            'check_at'=>$this->check_at ?? '',
            'checker'=>$this->checker ? new AdminOutResource($this->checker) : '',

            'approve_by'=>$this->approve_by ?? '',
            'approve_at'=>$this->approve_at ?? '',
            'approver'=>$this->approver ? new AdminOutResource($this->approver) : '',

            'reject_by'=>$this->reject_by,
            'reject_at'=>$this->reject_at ?? '',
            'reject_note' => $this->reject_note??'',
            'rejecter'=>$this->rejecter ? new AdminOutResource($this->rejecter) : '',


            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'permintaan_pembelian_details'=>PermintaanPembelianDetailResource::collection($this->whenLoaded('permintaan_pembelian_details')),
        ];

    }
}
