<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $fillable = ['key', 'value'];

    public function action(){
        return $this->belongsTo('App\Action');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function opportunity(){
        return $this->belongsTo('App\Opportunity');
    }
}
