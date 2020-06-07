<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'unique_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(){
        return $this->belongsTo('App\Role');
    }

    public function tags(){
        return $this->belongsToMany('App\Tag');
    }

    public function saved_opportunities(){
        return $this->belongsToMany('App\Opportunity');
    }

    public function premium_subscription(){
        return $this->hasOne('App\Transaction');
    }
    public function plus_subscription(){
        return $this->hasMany('App\PlusTransaction');
    }

    public function details(){
        return $this->hasOne('App\UserDetail');
    }

    public function social_accounts(){
        return $this->hasMany('App\UserSocial');
    }

    public function opportunities(){

    }

    public function validateForPassportPasswordGrant($password)
    {
        //$owerridedPassword = 'password';
        //return Hash::check($password, $this->password);
        return true;
    }

    public function findForPassport($username)
    {
        return $this->where('unique_id', $username)->first();
    }

    public function actions(){
        return $this->hasMany('App\ActionUser');
    }
}
