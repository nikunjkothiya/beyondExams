<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id', 'language_id', 'firstname', 'lastname', 'college', 'city', 'gpa', 'qualification_id', 'discipline_id', 'country_id'
    ];
}
