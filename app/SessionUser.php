<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SessionUser extends Model
{
    protected $fillable = ['session_id', 'user_id'];
    public $timestamps = false;
    public $table = "session_user";
}
