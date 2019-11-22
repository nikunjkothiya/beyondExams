<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    protected $fillable = ['discipline'];

    public function users(){
        return $this->hasOne('App\User');
    }
}
