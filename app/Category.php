<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'title',
        'user_id',
        'level',
        'parent_id',
        'previous_id',
        'image_url'
    ];

    public function toggle_visibility(){
        $this->visibility = !$this->visibility;
    }
}
