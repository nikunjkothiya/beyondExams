<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Language;
use App\Opportunity;

class OpportunityController extends Controller
{
    protected $languages;

    public function __construct(){
        $this->languages = Language::all();
    }

    public function get_opp($id){
    	try{
            $opportunity = Opportunity::findOrFail($id);
    	}
    	catch(Exception $e){
    		abort(404);
    	}
        return view('pages.opportunity',['opportunity'=>$opportunity,'languages'=>$this->languages]);
    }
}
