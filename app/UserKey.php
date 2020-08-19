<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserKey extends Model
{
    protected $fillable = [
        'key_id', 'user_id','transaction_id'
    ];
    public $timestamps = false;
    public function key(){
        return $this->belongsTo('App\Key', 'key_id');
    }
    
    public function user(){
        return $this->belongsTo('App\User', 'user_id');
    }

    public function txn(){
        return $this->belongsTo('App\Transaction', 'transaction_id');
    }
}
