<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PremiumTxn extends Model
{
    protected $table = 'premium_txn';
    protected $fillable = ['txn_id','user_id','plan_id','valid'];

    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function plan(){
    	return $this->belongsTo('App\PremiumPlan');
    }
}
