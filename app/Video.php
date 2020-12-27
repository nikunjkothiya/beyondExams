<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    //
    protected $fillable = [
        'url'
    ];

    public function num_likes(){
        return $this->likes()->count();
    }

    public function likes(){
        return $this->belongsToMany('App\User', 'user_video')->where('type', 'liked');
    }
}
