<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = ['resource_id', 'structure'];

    protected $casts = ['structure' => 'json'];
}
