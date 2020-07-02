<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class UserRole extends Model
{
    protected $fillable = [
        'user_id','is_user','is_mentor'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
