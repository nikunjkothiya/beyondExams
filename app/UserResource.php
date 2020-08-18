<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Resource;

class UserResource extends Model
{
    protected $fillable = [
        'user_id', 'resource_id', 'time_spent'
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function resource(){
        return $this->belongsTo('App\Resource', 'resource_id');
    }
}
