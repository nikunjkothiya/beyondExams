<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PremiumTxn extends Model
{
    protected $table = 'premium_txn';
    protected $fillable = ['user_id','plan_id','end_date'];

    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function plan(){
    	return $this->belongsTo('App\PremiumPlan');
    }
}
