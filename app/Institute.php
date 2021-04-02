<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    protected $table = 'institutes';
    protected $guarded = [];
    protected $with = ['institute'];

    public function institute() {
        return $this->hasOne('App\EducationUser','institutes_id','id');
    }
}
