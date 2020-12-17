<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['message', 'resource_id', 'user_id'];

    public function user(){
        return $this->belongsTo('App\User');
    }
}
