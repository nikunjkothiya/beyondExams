<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    protected $fillable = ['qualification'];

    public function users(){
        return $this->hasOne('App\User');
    }
}
