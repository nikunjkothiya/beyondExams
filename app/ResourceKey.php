<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Key;
use App\Resource;

class ResourceKey extends Model
{
    protected $fillable = [
        'resource_id', 'key_id'
    ];
    public $timestamps = false;
    public function key(){
        return $this->belongsTo('App\Key', 'key_id');
    }
    
    public function resource(){
        return $this->belongsTo('App\Resource', 'resource_id');
    }
    
}
