<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestScore extends Model
{
    protected $fillable = ['score'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function test(){
        return $this->belongsTo('App\Test');
    }
}
