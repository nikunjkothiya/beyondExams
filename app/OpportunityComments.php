<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpportunityComments extends Model
{
    protected $table = 'opportunity_comments';
    protected $opportunity_id = 'opportunity_id';
    protected $comment_id = 'comment_id';
    public $timestamps = true;
}
