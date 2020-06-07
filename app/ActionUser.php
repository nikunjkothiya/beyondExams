<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionUser extends Model
{
    protected $fillable = ['user_id', 'action_id'];

    public function action(){
        return $this->belongsTo('App\Action');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function properties(){
        return $this->hasMany('App\ActionProperty');
    }
}
