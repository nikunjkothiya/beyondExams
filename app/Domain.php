<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $table = 'domains';
    protected $guarded = [];

    public function users() {
        return $this->belongsToMany('App\User', 'domain_user')->withTimestamps();
    }
}
