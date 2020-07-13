<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Key;
use App\User;

class UserKey extends Model
{
    protected $fillable = [
        'key_id', 'user_id'
    ];
    public $timestamps = false;
    public function key(){
        return $this->belongsTo('App\Key', 'key_id');
    }
    
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }
}
