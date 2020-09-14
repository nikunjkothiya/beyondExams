<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    public $timestamps = false;

    public function key(){
        return $this->belongsToMany('App\Opportunity');
    }
}
