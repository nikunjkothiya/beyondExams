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

    public function tags(){
        return $this->belongsToMany('App\Tag');
    }

    public function tag_types(){
        
    }

    public function fund_type(){
    	return $this->belongsTo('App\FundType');
    }

    public function location(){
    	return $this->belongsTo('App\OpportunityLocation');
    }

    public function eligible_regions(){
    	return $this->belongsToMany('App\EligibleRegion', 'eligible_region_opportunity', 'opportunity_id', 'eligible_region_id');
    }
}
