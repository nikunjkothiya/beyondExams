<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{

    protected $fillable = ['url', 'title', 'type_id'];

    public function resource(){
        return $this->belongsTo('App\Resource');
    }
}
