<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\KeyPrice;
use App\User;
use App\UserKey;
use App\ResourceKey;

class Key extends Model
{
    protected $fillable = [
        'name', 'author_id'
    ];

    public function key_price(){
        return $this->hasOne('App\KeyPrice');
    }

    public function author(){
        return $this->belongsTo('App\User');
    }

    public function user_key(){
        return $this->hasMany('App\UserKey');
    }

    public function resource_key(){
        return $this->hasMany('App\ResourceKey');
    }

}
