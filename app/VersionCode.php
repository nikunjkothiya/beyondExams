<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VersionCode extends Model
{
    protected $fillable = [
        'version_code','version_name'
    ];
}
