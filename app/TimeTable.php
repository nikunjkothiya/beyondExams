<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model
{
    protected $table = 'timetables';
    protected $guarded = [];

    public function teacher(){
        return $this->belongsTo('App\User','teacher_id');
    }

    public function classroom(){
        return $this->belongsTo('App\chat','chat_id');
    }

}
