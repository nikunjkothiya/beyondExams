<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Organisation extends Model
{
    use HasApiTokens, Notifiable;

    //

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'unique_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    public function details()
    {
        return $this->hasOne('App\OrganisationDetail');
    }

    public function social_accounts()
    {
        return $this->hasMany('App\OrganisationSocial');
    }

    public function findForPassport($username)
    {
        return $this->where('unique_id', $username)->first();
    }

    public function opportunities(){
        return $this->hasMany('App\Opportunity');
    }

    public function mentors(){
        return $this->belongsToMany('App\User', 'mentor_organisation');
    }
}
