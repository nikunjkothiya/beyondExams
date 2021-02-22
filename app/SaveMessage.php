<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaveMessage extends Model
{
    protected $table = "save_messages";
    protected $fillable = ['student_id', 'chat_message_id'];

    public function chat_message(){
        return $this->belongsTo('App\ChatMessage', 'chat_message_id');
    }

    public function receiver(){
        return $this->belongsTo('App\User', 'student_id');
    }
}
