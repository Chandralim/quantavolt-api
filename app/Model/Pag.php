<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pag extends Model
{
    use HasFactory;

    protected $primaryKey = 'no';
    protected $keyType    = 'string';


    function pag_details()
    {
        return $this->hasMany(PagDetail::class, 'pag_no', 'no');
    }

    function project()
    {
        return $this->belongsTo(Project::class, 'project_no', 'no');
    }

    function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    function updator()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    function pbg()
    {
        return $this->hasMany(Pbg::class, 'pag_no', 'no');
    }
}
