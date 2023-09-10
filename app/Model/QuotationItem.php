<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';
    protected $keyType  = 'string';

    function creator(){
        return $this->belongsTo(User::class, 'created_by','id');
    }

    function updator(){
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    function unit(){
        return $this->belongsTo(Unit::class, 'unit_code', 'code');
    }

    public function getSellingPriceAttribute()
    {
        return ( $this->purchase_price * $this->percent / 100 ) + $this->purchase_price + $this->shipping_cost;
    }
}
