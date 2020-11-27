<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = ['title', 'mcqs', 'resource_url'];

    protected $casts = ['mcqs' => 'json'];

    public function scores(){
        return $this->hasMany('App\TestScore');
    }
}
