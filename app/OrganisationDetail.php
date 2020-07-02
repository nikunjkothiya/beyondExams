<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganisationDetail extends Model
{
    protected $fillable = [
        'organisation_id', 'org_name', 'contact_person', 'branch', 'email', 'phone'
    ];
    //
}
