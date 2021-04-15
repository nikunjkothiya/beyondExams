<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class HistoryUserVidoes extends Pivot
{
    protected $table = 'history_user_videos';

    protected $guarded = [];

    public function usersVideosIds()
    {
        return $this->belongsToMany('App\UserVideo');
    }
    
}
