<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassMentor extends Model
{
    public function class(){
        return $this->belongsTo('App\ClassModel');
    }
}
