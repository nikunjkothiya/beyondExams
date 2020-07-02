<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Key;

class KeyPrice extends Model
{
    protected $fillable = [
        'key_id', 'price_inr', 'price_usd'
    ];

    public function key(){
        return $this->belongsTo('App\Key');
    }
}
