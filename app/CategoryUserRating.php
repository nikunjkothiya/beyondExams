<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryUserRating extends Pivot
{
    protected $guarded= [];
    protected $table = 'category_user_rating';

}
