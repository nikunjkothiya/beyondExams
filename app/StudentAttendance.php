<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $table = 'student_attendances';
    protected $guarded = [];

    public function student(){
        return $this->belongsTo('App\User','student_id');
    }

    public function classroom(){
        return $this->belongsTo('App\TimeTable','timetable_id');
    }
}
