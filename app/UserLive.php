<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLive extends Model
{
    protected $table = 'user_live';
    protected $fillable = ['user_id', 'peer_id', 'live'];
    public function user(){
        return $this->belongsTo('App\User');
    }
}
