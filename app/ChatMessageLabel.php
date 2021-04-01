<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatMessageLabel extends Model
{
    protected $table = 'chat_messages_labels';
    protected $guarded = [];

    public function message()
    {
        return $this->belongsTo('App\ChatMessage', 'chat_message_id');
    }
}
