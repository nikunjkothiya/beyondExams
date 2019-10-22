<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSocial extends Model
{
    protected $fillable = [
        'user_id', 'provider', 'provider_id'
    ];
}
