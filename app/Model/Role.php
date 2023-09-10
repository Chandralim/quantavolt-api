<?php

namespace App\Model\Internal;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  protected $connection= 'pgsql';
  protected $table = 'internal.roles';
  public $timestamps = false;

  // protected $primaryKey = 'visitor_id';
  // protected $keyType = 'string';

    // protected $fillable = [
    //     'code','name', 'no_acc','admin_code'
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'id',
    // ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    // protected $visible = [
    //     'id','title'
    // ];

    // public function setCodeAttribute($value)
    // {
    //     $this->attributes['code'] = strtoupper($value);
    // }
    // public function proof_of_expenditure()
    // {
    //   return $this->belongsTo(ProofOfExpenditure::class,'proof_of_expenditure_number','number');
    // }

    public function admins()
    {
      return $this->hasMany(Admin::class);
    }

    // public function role_permissions()
    // {
    //   return $this->hasMany(Role_Permission::class,'id','role_id');
    // }

    public function permissions()
    {
      return $this->belongsToMany(Permission::class,"internal.permission_role");
    }

}
