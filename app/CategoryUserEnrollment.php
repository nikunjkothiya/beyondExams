<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryUserEnrollment extends Pivot
{
    protected $guarded= [];
    protected $table = 'category_user_enrollment';

    public function categories(){
    	return $this->hasMany('App\Category','id','category_id');
    }

}
