<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageType extends Model
{
    public $timestamps = false;
    protected $fillable = ['type'];
}
