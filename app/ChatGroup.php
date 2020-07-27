<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    protected $fillable = ['chat_id', 'opportunity_id'];

    public function chat(){
        return $this->belongsTo('App\Chat', 'chat_id');
    }

    public function opportunity(){
        return $this->belongsTo('App\Opportunity', 'opportunity_id');
    }
}
