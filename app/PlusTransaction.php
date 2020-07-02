<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlusTransaction extends Model
{
    protected $fillable = ['user_id','opportunity_id','datetime','amount'];

    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function opportunity(){
    	return $this->belongsTo('App\Opportunity');
    }
}
