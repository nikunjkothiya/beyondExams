<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpportunityTranslations extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'description'];

    public function opportunity(){
        return $this->belongsTo('App\Opportunity');
    }
}
