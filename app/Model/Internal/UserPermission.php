<?php

namespace App\Model\Internal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'internal.user_permissions';

    protected $primaryKey = null;
    public $incrementing = false;
}
