<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class KeywordVideo extends Pivot
{
    protected $guarded= [];
    protected $table = 'keyword_video';
   
}
