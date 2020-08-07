<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserViewedOpportunity extends Model
{
    protected $fillable = ['user_id','opportunity_id'];
    public $timestamps = false;
    protected $table = 'user_viewed_opportunities';
}
