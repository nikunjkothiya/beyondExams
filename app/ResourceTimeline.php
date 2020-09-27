<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceTimeline extends Model
{
    protected $fillable = [
        'resource_id', 'user_id', 'priority'
    ];
}
