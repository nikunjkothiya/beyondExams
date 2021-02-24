<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCertificate extends Model
{
    protected $table = 'user_certicates';
    protected $guarded = [];

    public function user(){
        return $this->belongsTo('App\User','id','user_id');
    }
}


   


