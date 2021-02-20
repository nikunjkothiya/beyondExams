<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ses extends Model
{
    protected $table = 'ses';
    //
    public function videos(){
        return $this->hasOne('App\Video');
    }
}
