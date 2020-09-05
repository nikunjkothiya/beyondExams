<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = ['title', 'peer_id', 'host_id', 'user_limit', 'live', 'live_time', 'restricted', 'session_type'];

    public function user(){
        return $this->belongsToMany('App\User');
    }
    public function host(){
        return $this->hasOne('App\User', 'host_id');
    }
    public function type(){
        return $this->hasOne('App\SessionType', 'session_type');
    }
}