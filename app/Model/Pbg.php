<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pbg extends Model
{
    use HasFactory;

    protected $primaryKey = 'no';
    protected $keyType    = 'string';

    function pag()
    {
        return $this->belongsTo(Pag::class, 'pag_no', 'no');
    }

    function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    function updator()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    function pbg_details()
    {
        return $this->hasMany(PbgDetail::class, 'pbg_no', 'no');
    }
}
