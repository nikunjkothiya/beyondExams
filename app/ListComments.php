<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListComments extends Model
{
    protected $table = 'list_comments';
    protected $message = 'message';
    public $timestamps = true;
}
