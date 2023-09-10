<?php

namespace App\Model\Internal;

use Illuminate\Database\Eloquent\Model;

class PermintaanPembelian extends Model
{
  protected $connection= 'pgsql';
  protected $table = 'internal.permintaan_pembelians';
  public $timestamps = false;
  protected $primaryKey = 'no';
  protected $keyType = 'string';

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
    public function permintaan_pembelian_details()
    {
      return $this->hasMany(PermintaanPembelianDetail::class,"permintaan_pembelian_no","no");
    }

    public function submitter()
    {
      return $this->belongsTo(Admin::class,"submit_by","id");
    }

    public function checker()
    {
      return $this->belongsTo(Admin::class,"check_by","id");
    }

    public function approver()
    {
      return $this->belongsTo(Admin::class,"approve_by","id");
    }

    public function rejecter()
    {
      return $this->belongsTo(Admin::class,"reject_by","id");
    }

}
