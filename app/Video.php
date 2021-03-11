<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'url'
    ];

    public function num_likes()
    {
        return $this->likes()->count();
    }

    public function likes()
    {
        return $this->belongsToMany('App\User', 'user_video')->where('type', 'liked');
    }

    public function ses()
    {
	return $this->hasOne('App\Ses');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->wherePivot('type', 'history')->withTimestamps();
    }

    public function bookmarkByUser()
    {
        return $this->belongsToMany('App\User', 'bookmark_video')->withTimestamps();
    }
    
    public function duration_history()
    {
        return $this->hasMany('App\HistoryUserVidoes', 'video_id', 'id');
    }

    public function keywords()
    {
        return $this->belongsToMany('App\Keyword')->withTimestamps();
    }
}
