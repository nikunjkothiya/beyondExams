<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpportunityRepresentative extends Model
{
    public $timestamps = false;
    protected $fillable = ['opportunity_id', 'representative_id'];

    public function opportunity(){
        return $this->belongsTo('App\Opportunity', 'opportunity_id');
    }

    public function representative(){
        return $this->belongsTo('App\User', 'representative_id');
    }
}
