<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DomainUser extends Pivot
{
    protected $table = 'domain_user';
    protected $guarded = [];

}
