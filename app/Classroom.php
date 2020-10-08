<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{

    protected $dates = [
        'start_datetime',
    ];

    public function students(){
        return $this->belongsToMany('App\User', 'classroom_students', 'classroom_id', 'student_id');
    }

    public function teacher(){
        return $this->hasOne('App\User');
    }
}
