<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    public $timestamps = false;

    public function key(){
        return $this->belongsToMany('App\Opportunity');
    }

    public function users(){
        return $this->belongsToMany('App\User');
    }

    public function videos() {
        return $this->belongsToMany('App\Video')->withTimestamps();
    }
}
