<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatReview extends Model
{
    protected $table = 'chat_reviews';
    protected $guarded = [];

    public function student(){
        return $this->belongsTo('App\User','id','student_id');
    }

}
