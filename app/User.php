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
        'name', 'email', 'password', 'role_id', 'unique_id', 'user_role'
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
        return $this->hasOne('App\UserRole');
    }

    public function chats(){
        return $this->belongsToMany('App\Chat');
    }

    public function mentor_verification(){
        return $this->hasOne('App\MentorVerification');
    }

    public function tags(){
        return $this->belongsToMany('App\Tag');
    }

    public function domains(){
        return $this->belongsToMany('App\DomainUser');
    }

    public function saved_opportunities(){
        return $this->belongsToMany('App\Opportunity');
    }

    public function viewed_opportunity(){
        return $this->belongsToMany('App\UserViewedOpportunity');
    }

    public function premium_subscription(){
        return $this->hasOne('App\Transaction');
    }
    public function plus_subscription(){
        return $this->hasMany('App\PlusTransaction');
    }
    public function premium_txn(){
        return $this->hasOne('App\PremiumTxn');
    }

    public function details(){
        return $this->hasOne('App\UserDetail');
    }

    public function social_accounts(){
        return $this->hasMany('App\UserSocial');
    }

    public function opportunities(){
        return $this->belongsToMany('App\Opportunity');
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

    public function analytics(){
        return $this->hasMany('App\Analytics');
    }

    public function user_key(){
        return $this->hasMany('App\UserKey');
    }

    public function user_resource(){
        return $this->hasMany('App\UserResource');
    }

    public function student_firebase_id(){
        return $this->hasMany('App\StudentFirebase');
    }

    public function admin_firebase_id(){
        return $this->hasMany('App\AdminFirebase');
    }

    public function is_admin(){
        return $this->role()->is_admin;
    }
}
