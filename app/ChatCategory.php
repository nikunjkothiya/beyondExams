<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatCategory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'chat_id', 'category_id'
    ];
}
