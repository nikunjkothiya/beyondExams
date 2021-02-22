<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ses extends Model
{
    protected $table = 'ses';
    //
    public function video(){
        return $this->belongsTo('App\Video');
    }
}
