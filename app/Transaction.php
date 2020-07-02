<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['user_id','transaction_id','product_id','datetime'];

    public function user(){
    	return $this->belongsTo('App\User');
    }

    public function product(){
    	return $this->belongsTo('App\Product');
    }
}
