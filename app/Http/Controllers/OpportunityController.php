<?php

namespace App\Http\Controllers;

use Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\SubscriptionController;
use App\Language;
use App\Opportunity;

class OpportunityController extends Controller
{
    protected $languages;

    public function __construct(){
        $this->languages = Language::all();
        $this->txnflag = new SubscriptionController;
    }

    public function get_opp($slug){
    	try{
            $opportunity = Opportunity::where('slug',$slug)->firstOrFail();
            $flag = 0;
            if(Auth::check()){
                $flag = $this->txnflag->check_subscription(Auth::user()->id);
            }
    	}
    	catch(Exception $e){
    		abort(404);
    	}
        return view('pages.opportunity',['opportunity'=>$opportunity,'languages'=>$this->languages,'txnflag'=>$flag]);
    }
}
