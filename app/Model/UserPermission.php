<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $primaryKey = null;
    public $incrementing = false;
}
