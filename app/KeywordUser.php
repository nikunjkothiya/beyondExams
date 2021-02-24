<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class KeywordUser extends Pivot
{
    protected $guarded= [];
    protected $table = 'keyword_user';

}
