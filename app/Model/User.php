<?php

namespace App\Model;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Helpers\MyLib;

class User extends Authenticatable
{
    use Notifiable;

    protected $connection= 'pgsql';
    protected $table = 'users';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name', 'email', 'password',
    // ];

    // /**
    //  * The attributes that should be hidden for arrays.
    //  *
    //  * @var array
    //  */
    // protected $hidden = [
    //     'password', 'token',
    // ];

    // /**
    //  * The attributes that should be cast to native types.
    //  *
    //  * @var array
    //  */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function generateToken()
    {
      $this->token      = Str::random(200).$this->id.Str::random(5)."/#".MyLib::getMillis();
      $this->updated_at = date("Y-m-d H:i:s");
      $this->save();
      return $this->token;
    }

    // public function role()
    // {
    //   return $this->hasOne(Role::class,'id','role_id');
    // }

    // public function role_permissions()
    // {
    //   return $this->hasMany(Role_Permission::class,'role_id','role_id');
    // }

    public function action_permissions()
    {
      return $this->belongsToMany(ActionPermission::class,'user_permissions')->orderBy('name', 'asc')->orderBy('ordinal', 'asc');
    }

    public function data_permissions()
    {
      return $this->belongsToMany(DataPermission::class,'user_permissions')->orderBy('table_name', 'asc')->orderBy('field_name', 'asc')->orderBy('status', 'asc');
    }

    public function listPermissions(){
      $ap = $this->action_permissions->pluck("in_one_line")->toArray();
      $dp = $this->data_permissions->pluck("in_one_line")->toArray();
      return array_merge($ap,$dp);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class,'no',"employee_no");
    }
}
