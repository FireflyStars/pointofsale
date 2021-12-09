<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRoles(){
        $roles_ids=[];
        $roles_ids[]=$this->role_id;
        $user_roles=DB::table('user_roles')->select('role_id')->where('user_id','=',$this->id)->get();
        foreach ($user_roles as $role){
            $roles_ids[]=$this->role_id;
        }
        $roles=DB::table('roles')->select('name')->whereIn('id',$roles_ids)->get();
        return $roles;
    }

    public function affiliate(){
        return $this->belongsTo(Affiliate::class);
    }

}
