<?php

namespace App\Model;

use App\ProjectAdditional;
use App\ProjectWorker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $primaryKey = 'no';
    protected $keyType    = 'string';

    function creator() 
    {
       return $this->belongsTo(User::class, 'created_by', 'id'); 
    }

    function updator()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_code', 'code');
    }

    function project_materials()
    {
        return $this->hasMany(ProjectMaterial::class, 'project_no' , 'no');
    }

    function project_workers()
    {
        return $this->hasMany(ProjectWorker::class, 'project_no' , 'no');
    }

    function project_working_tools()
    {
        return $this->hasMany(ProjectWorkingTool::class, 'project_no' , 'no');
    }

    function project_additionals()
    {
        return $this->hasMany(ProjectAdditional::class, 'project_no', 'no');
    }



}
