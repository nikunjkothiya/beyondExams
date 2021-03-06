<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    protected $fillable = ['search_term'];

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany('App\User', 'search_user')->withTimestamps();
    }
}
