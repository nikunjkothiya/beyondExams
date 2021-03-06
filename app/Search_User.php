<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Search_User extends Pivot
{
    protected $table = 'search_user';
    protected $guarded = [];
}
