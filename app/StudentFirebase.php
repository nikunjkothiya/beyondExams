<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentFirebase extends Model
{
    protected $fillable = ['user_id', 'deviceId', 'firebaseId'];

    protected $table = 'student_firebase';

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
}
