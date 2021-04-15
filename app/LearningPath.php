<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LearningPath extends Model
{
    //
    protected $fillable = ['category_id', 'video_id', 'ordering'];

    public function video(){
        return $this->belongsTo('App\Video');
    }
}
