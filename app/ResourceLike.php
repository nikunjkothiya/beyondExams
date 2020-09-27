<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResourceLike extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'resource_id', 'user_id'
    ];

    public function user(){
        return $this->belongsTo('App\UserDetail', 'user_id', 'user_id');
    }
}
