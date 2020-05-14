<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    //
    protected $fillable = ['file_url', 'thumbnail_url', 'file_type', 'duration', 'title', 'author_id'];

    public function filetype(){
        return $this->belongsTo('App\FileType');
    }

    public function user(){
        return $this->belongsTo('App\User', 'author_id');
    }
}
