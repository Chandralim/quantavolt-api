<?php

namespace App\Model\Main;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberInstitute extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'public.member_institutes';

    protected $primaryKey = null;
    public $incrementing = false;

    public function member()
    {
        return $this->belongsTo(Member::class, "member_id", "id");
    }

    public function institute()
    {
        return $this->belongsTo(Institute::class, "institute_id", "id");
    }
}
