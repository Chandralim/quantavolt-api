<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransaction extends Model
{
    use HasFactory;
    protected $primaryKey = null;

    function item()
    {
        return $this->belongsTo(Item::class, "item_code", "code");
    }
}
