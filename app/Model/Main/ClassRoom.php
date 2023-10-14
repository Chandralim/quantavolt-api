<?php

namespace App\Model\Main;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'public.class_rooms';

    public function homeroom_teacher()
    {
        return $this->belongsTo(Member::class, "homeroom_teacher_id", "id");
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class, "institute_id", "id");
    }
}
