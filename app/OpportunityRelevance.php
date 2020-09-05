<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpportunityRelevance extends Model
{
    protected $fillable = ['opportunity_id', 'user_id', 'score'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function opportunity(){
        return $this->belongsTo('App\Opportunity');
    }
}
