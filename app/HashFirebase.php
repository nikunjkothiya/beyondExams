<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HashFirebase extends Model
{
    protected $fillable = ['deviceId', 'firebaseId'];

    protected $table = 'hash_firebase';

    public function chat(){
        return $this->belongsToMany('App\Chat');
    }
}
