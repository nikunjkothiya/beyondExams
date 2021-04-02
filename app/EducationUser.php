<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EducationUser extends Model
{
    protected $table = 'education_user';
    protected $guarded = [];

    public function institute_name() {
        return $this->hasOne('App\Institute','id','institutes_id');
    }

    public function standard_name() {
        return $this->hasOne('App\EducationStandard','id','education_standard_id');
    }
}
