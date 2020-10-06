<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    public $table = 'classes';

    public function mentor(){
        return $this->hasOne('App\ClassMentor');
    }
}
