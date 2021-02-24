<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherDocument extends Model
{
    protected $table = 'teacher_documents';
    protected $guarded = [];

    public function user(){
        return $this->belongsTo('App\User','id','creator_id');
    }

    public function timetable(){
        return $this->belongsTo('App\TimeTable','id','timetable_id');
    }
}
