<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PremiumValidity extends Model
{
    protected $fillable = ['user_id','end_date'];

    public function user(){
    	return $this->belongsTo('App\User');
    }
}
