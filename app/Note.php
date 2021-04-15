<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['title', 'url', 'resource_url'];

    public function user(){
        return $this->belongsTo('App\User');
    }
}
