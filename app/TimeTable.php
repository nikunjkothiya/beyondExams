<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model
{
    protected $table = 'timetables';
    protected $guarded = [];

    public function user(){
        return $this->belongsTo('App\User','id','teacher_id');
    }

    public function chats(){
        return $this->belongsToMany('App\chat','id','chat_id');
    }
}
