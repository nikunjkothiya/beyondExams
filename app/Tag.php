<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['tag','tag_type_id'];

    public function users(){
        return $this->belongsToMany('App\User');
    }

    public function opportunities(){
    	return $this->belongsToMany('App\Opportunity');
    }

    public function type(){
    	return $this->belongsTo('App\TagType');
    }
}
