<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoAnnotation extends Model
{
    protected $table = 'video_annotations';
    protected $guarded = [];
    
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function total_vote(){
        return $this->hasOne('App\VideoNoteTotalVote','video_annotation_id','id');
    }
}
