<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    protected $fillable = ['chat_id', 'user_id', 'role_id', 'unread'];

    public function chat(){
        return $this->belongsTo('App\Chat', 'chat_id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function role(){
        return $this->belongsTo('App\Role', 'role_id');
    }
}
