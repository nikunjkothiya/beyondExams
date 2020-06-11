<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpportunityLocation extends Model
{
    protected $fillable = ['location'];

    public function opportunity(){
        return $this->hasMany('App\Opportunity');
    }
}
