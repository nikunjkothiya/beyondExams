<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    protected $table = 'teacher_attendances';
    protected $guarded = [];

    public function teacher(){
        return $this->belongsTo('App\User','teacher_id');
    }

    public function lecture(){
        return $this->belongsTo('App\TimeTable','timetable_id');
    }
}
