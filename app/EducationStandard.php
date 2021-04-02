<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EducationStandard extends Model
{
    protected $table = 'education_standards';
    protected $guarded = [];
    protected $with = ['standard'];

    public function standard() {
        return $this->hasOne('App\EducationUser','education_standard_id','id');
    }
}
