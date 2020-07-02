<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class MentorVerification extends Model
{
    protected $fillable = [
        'user_id', 'is_verified'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
