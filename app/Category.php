<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'level',
        'parent_id'
    ];

    public function toggle_visibility(){
        $this->visibility = !$this->visibility;
    }
}
