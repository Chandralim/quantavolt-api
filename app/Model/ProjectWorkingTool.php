<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectWorkingTool extends Model
{
    use HasFactory;

    public $timestamps    = false;

    protected $primaryKey = null;
    public $incrementing  = false;

    function creator() 
    {
       return $this->belongsTo(User::class, 'created_by', 'id'); 
    }

    function updator()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    function project()
    {
        return $this->belongsTo(Project::class, "project_no", "no");
    }

    function unit()
    {
        return $this->belongsTo(Unit::class, "unit_code", "code");
    }

    function item()
    {
        return $this->belongsTo(Item::class, "item_code", "code");
    }

}
