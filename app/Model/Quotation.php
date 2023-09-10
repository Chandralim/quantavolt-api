<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;
    protected $primaryKey = 'no';
    protected $keyType    = 'string';

    function quotation_details()
    {
        return $this->hasMany(QuotationDetail::class,'quotation_no','no');    
    }

    function creator() 
    {
       return $this->belongsTo(User::class, 'created_by', 'id'); 
    }

    function updator()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }


}
