<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganisationSocial extends Model
{
    protected $fillable = [
        'organisation_id', 'provider', 'provider_id'
    ];

    public function user(){
        return $this->belongsTo(Organisation::class);
    }
}
