<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PremiumPlan extends Model
{
    protected $fillable = ['plan_name','price','months','currency_id'];

    public function currency(){
        return $this->belongsTo('App\Currency');
    }

    public function premium_subscription(){
        return $this->hasMany('App\PremiumTxn');
    }
    
}
