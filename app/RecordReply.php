<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecordReply extends Model
{
    protected $table = 'reply';
    protected $user_id = 'user_id';
    protected $comment_id = 'comment_id';
    protected $content = 'comment';
    public $timestamps = true;
}
