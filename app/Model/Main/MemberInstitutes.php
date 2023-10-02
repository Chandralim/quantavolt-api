<?php

namespace App\Model\Main;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberInstitutes extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'public.member_institutes';

    protected $primaryKey = null;
    public $incrementing = false;

    public function member()
    {
        $this->belongsTo(Member::class, "id", "member_id");
    }

    public function institute()
    {
        $this->belongsTo(Institute::class, "id", "institute_id");
    }
}
