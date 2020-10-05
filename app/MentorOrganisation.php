<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MentorOrganisation extends Model
{
    public $table = 'mentor_organisation';
    public $timestamps = false;

    public function mentor(){
        return $this->belongsTo('App\User');
    }

    public function organisation(){
        return $this->belongsTo('App\Organisation');
    }
}
