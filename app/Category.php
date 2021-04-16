<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'level',
        'parent_id',
        'previous_id',
        'image_url',
        'slug'
    ];

    public function toggle_visibility(){
        $this->visibility = !$this->visibility;
    }

    public function keywords() {
        return $this->belongsToMany('App\Keyword')->withTimestamps();
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
