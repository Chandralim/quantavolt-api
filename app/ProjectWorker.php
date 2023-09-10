<?php

namespace App;

use App\Model\Employee;
use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectWorker extends Model
{
    use HasFactory;

    public $timestamps    = false;

    protected $primaryKey = null;
    public $incrementing  = false;
    
    function project()
    {
        return $this->belongsTo(Project::class, "project_no", "no");
    }


    function employee()
    {
        return $this->belongsTo(Employee::class, "employee_no", "no");
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
