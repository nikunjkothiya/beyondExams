<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassroomChatMessage extends Model
{
    protected $table = 'classroom_chat_message';
    protected $guarded = [];

    public function chat_message(){
        return $this->belongsTo('App\ChatMessage', 'chat_message_id');
    }

  /*   public function timetable()
    {
        return $this->hasOne('App\TimeTable', 'id', 'timetable_id ');
    } */
}
