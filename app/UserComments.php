<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserComments extends Model
{
    protected $table = 'user_comments';
    protected $user_id = 'user_id';
    protected $comment_id = 'comment_id';
    public $timestamps = true;
}
