<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatOperator extends Model
{
    protected $fillable = ['chat_id', 'operator_id'];

    public function chat(){
        return $this->belongsTo('App\Chat', 'chat_id');
    }

    public function operator(){
        return $this->belongsTo('App\User', 'operator_id');
    }
}
