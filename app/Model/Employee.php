<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $primaryKey = 'no';

    function creator() 
    {
       return $this->belongsTo(User::class, 'created_by', 'id'); 
    }

    function updator()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
