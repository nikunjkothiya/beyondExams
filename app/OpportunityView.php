<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpportunityView extends Model
{
    protected $fillable = ['views'];

    public function opportunity(){
        return $this->belongsTo('App\Opportunity');
    }
}
