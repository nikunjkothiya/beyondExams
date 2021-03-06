<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserVideo extends Pivot
{
    protected $table = 'user_video';
    protected $guarded = [];

   public function watchHistoryUsers(){
       return $this->belongsToMany('App\HistoryUserVidoes', 'history_user_videos','id','user_video_id')->withPivot('type', 'history');
   }
}
