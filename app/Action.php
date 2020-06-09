<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ['event'];

    public function analytics(){
        return $this->hasMany('App\Analytics');
    }
}
