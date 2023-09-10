<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PbgDetail extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $primaryKey = null;
    public $incrementing = false;

    function item()
    {
        return $this->belongsTo(Item::class,"item_code","code");    
    }

    // function unit() 
    // {
    //     return $this->belongsTo(Unit::class, 'unit_code', 'code');    
    // }

    function pbg()
    {
        return $this->belongsTo(Pbg::class, 'pbg_no', 'no');
    }
}
