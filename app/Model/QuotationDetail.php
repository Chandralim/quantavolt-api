<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationDetail extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = null;
    public $incrementing = false;

    function quotation_item()
    {
        return $this->belongsTo(QuotationItem::class,"quotation_item_code","code");    
    }
}
