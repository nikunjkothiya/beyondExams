<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AnnotationUserReport extends Pivot
{
    protected $guarded= [];
    protected $table = 'annotation_user_report'; 
}
