<?php

namespace App\Model\Internal;

use Illuminate\Database\Eloquent\Model;

class PermintaanPembelianDetail extends Model
{
  protected $connection= 'pgsql';
  protected $table = 'internal.permintaan_pembelian_details';
  
  public $timestamps = false;

  protected $primaryKey = null;
  public $incrementing = false;


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
    // public function roles()
    // {
    //   return $this->belongsToMany(Role::class,"internal.permission_role");
    // }

    public function creator()
    {
      return $this->belongsTo(Admin::class,"created_by","id");
    }
    public function updator()
    {
      return $this->belongsTo(Admin::class,"updated_by","id");
    }

}
