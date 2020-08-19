<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MentorDetail extends Model
{
    protected $fillable = [
        'user_id', 'firstname', 'lastname', 'email', 'designation', 'organisation', 'profile_link'
    ];
}
