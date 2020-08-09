<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = ['json_content', 'title'];

    protected $casts = ['json_content' => 'json'];

    public function resource(){
        return $this->belongsTo('App\Resource');
    }
}
