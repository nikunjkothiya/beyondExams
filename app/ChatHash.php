<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatHash extends Model
{
    protected $fillable = [
        'chat_id', 'hashcode'
    ];

    public $timestamps = false;

    public function chat(){
        return $this->hasOne('App\Chat');
    }
}
