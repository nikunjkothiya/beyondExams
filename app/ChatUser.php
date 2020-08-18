<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    protected $fillable = ['chat_id', 'user_id', 'role_id', 'unread'];
    protected $table = 'chat_user';
    public function chat(){
        return $this->belongsTo('App\Chat', 'chat_id');
    }

    public function user(){
        return $this->belongsToMany('App\User', 'user_id');
    }

    public function role(){
        return $this->belongsTo('App\Role', 'role_id');
    }
}
