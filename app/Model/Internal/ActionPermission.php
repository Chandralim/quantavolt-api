<?php

namespace App\Model\Internal;

use Illuminate\Database\Eloquent\Model;

class ActionPermission extends Model
{
  protected $connection= 'pgsql';
  protected $table = 'internal.action_permissions';
  public $timestamps = true;

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
  // public function users()
  // {
  //   return $this->belongsToMany(User::class,"user_permissions");
  // }

  public function getInOneLineAttribute()
  {
      return 'ap-' . $this->name . '-' . $this->action;
  }
}
