<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    //
    protected $fillable = ['file_url', 'thumbnail_url', 'file_type', 'duration', 'title', 'author_id'];

    public function filetype(){
        return $this->belongsTo('App\FileType');
    }

    public function user(){
        return $this->belongsTo('App\User', 'author_id');
    }

    public function resource_key(){
        return $this->hasMany('App\ResourceKey');
    }

    public function user_resource(){
        return $this->hasOne('App\UserResource');
    }

    public function comments(){
        return $this->hasMany('App\ResourceComment');
    }

    public function likes(){
        return $this->hasOne('App\ResourceLike');
    }

    public function notes(){
        return $this->hasMany('App\Note');
    }

    public function tests(){
        return $this->hasMany('App\Test');
    }
}
