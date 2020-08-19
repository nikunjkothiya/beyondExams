<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeyPrice extends Model
{
    protected $fillable = ['key_id','currency_id','price'];
    public $timestamps = false;
    public function key(){
        return $this->belongsTo('App\Key');
    }

    public function currency(){
        return $this->belongsTo('App\Currency');
    }
}
