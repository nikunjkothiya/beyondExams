<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['chat_id', 'message', 'type_id ', 'sender_id', 'is_child'];

    public function chat(){
        return $this->belongsTo('App\Chat', 'chat_id');
    }

    public function type(){
        return $this->belongsTo('App\MessageType', 'type_id');
    }

    public function sender(){
        return $this->belongsTo('App\User', 'sender_id');
    }

    public function fromContact()
    {
        return $this->hasOne('App\User', 'id', 'sender_id');
    }
}
