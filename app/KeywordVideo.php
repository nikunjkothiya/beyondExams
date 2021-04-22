<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class KeywordVideo extends Pivot
{
    protected $guarded= [];
    protected $table = 'keyword_video';

    public function keyword()
    {
        return $this->hasOne('App\Keyword','id','keyword_id');
    }

    public function video_url()
    {
        return $this->hasOne('App\Video','id','video_id');
    }
   
}
