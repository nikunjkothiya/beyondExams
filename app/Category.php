<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'user_id',
        'level',
        'previous_id',
        'image_url'
    ];

    public function toggle_visibility(){
        $this->visibility = !$this->visibility;
    }
}
