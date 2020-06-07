<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActionProperty extends Model
{
    protected $fillable = ['act_id','key', 'value'];

    public function action_user(){
        return $this->belongsTo('App\ActionUser');
    }
}
