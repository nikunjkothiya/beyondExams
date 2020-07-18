<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'creator_id', 'title', 'is_group', 'is_support' 
    ];

    public function opportunity(){
        return $this->hasOne('App\Opportunity');
    }

    public function group(){
        return $this->hasOne('App\ChatGroup');
    }

    public function creator(){
        return $this->belongsTo('App\User', 'creator_id');
    }

    public function operators(){
        return $this->hasMany('App\ChatOperator');
    } 

    public function messages(){
        return $this->hasMany('App\ChatMessage');
    }
}
