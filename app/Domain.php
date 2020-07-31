<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    public $timestamps = false;
    protected $fillable = ['domain'];
}
