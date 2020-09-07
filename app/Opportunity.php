<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Opportunity extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['image','link','deadline','fund_type_id','opportunity_location_id','slug'];

    public function organisation(){
        return $this->belongsTo('App\Organisation', '', '');
    }

    public function opportunity_translations(){
        return $this->hasMany('App\OpportunityTranslations');
    }

    public function tags(){
        return $this->belongsToMany('App\Tag');
    }

    public function chats(){
        return $this->hasMany('App\Chat');
    }

    public function tag_types(){

    }

    public function fund_type(){
    	return $this->belongsTo('App\FundType');
    }

    public function plus_subscription(){
        return $this->hasMany('App\PlusTransaction');
    }

    public function location(){
    	return $this->belongsTo('App\OpportunityLocation', 'opportunity_location_id');
    }

    public function eligible_regions(){
    	return $this->belongsToMany('App\EligibleRegion', 'eligible_region_opportunity', 'opportunity_id', 'eligible_region_id');
    }

    public function views(){
        return $this->hasMany('App\OpportunityView');
    }

    public function analytics(){
        return $this->hasMany('App\Analytics');
    }

    public function relevance(){
	return $this->hasOne('App\OpportunityRelevance');
    }
}
