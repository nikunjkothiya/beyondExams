<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentHomework extends Model
{
    protected $table = 'student_homeworks';
    protected $guarded = [];

    public function user(){
        return $this->belongsTo('App\User','student_id');
    }

    public function timetable(){
        return $this->belongsTo('App\TimeTable','timetable_id');
    }
}
