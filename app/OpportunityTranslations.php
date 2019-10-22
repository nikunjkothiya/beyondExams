<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpportunityTranslations extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'description'];
}
