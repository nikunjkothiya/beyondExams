<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLastLogin extends Model
{
    public $table = 'user_last_login';
    protected $fillable = [
        'user_id', 'updated_at'
    ];
}
