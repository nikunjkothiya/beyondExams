<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainUser extends Model
{
    public $timestamps = false;
    protected $table = 'domain_user';
    protected $fillable = ['domain_id','user_id'];

    public function users(){
        return $this->belongsToMany('App\User','user_id');
    }

    public function domains(){
        return $this->belongsTo('App\Domain', 'domain_id');
    }
}
