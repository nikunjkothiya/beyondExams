<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = ['event'];

    public function action_user(){
        return $this->hasMany('App\ActionUser');
    }
}
