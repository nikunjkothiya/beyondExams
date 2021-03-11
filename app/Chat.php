<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'title', 'creator_id', 'is_support'
    ];

    public function creator(){
        return $this->belongsTo('App\User', 'creator_id');
    }

    public function receiver(){
        return $this->belongsTo('App\User', 'receiver_id');
    }

    public function users(){
        return $this->belongsToMany('App\User');
    }

    public function messages(){
        return $this->hasMany('App\ChatMessage');
    }
    public function reviews(){
        return $this->hasMany('App\ChatReview','chat_id','id');
    }
}
