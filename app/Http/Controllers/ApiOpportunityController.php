<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use App\Language;
use App\Opportunity;
use App\PlusTransaction;
use App\User;
use Auth;

class ApiOpportunityController extends Controller
{
    protected $languages;
    private $apiResponse;
    
    public function __construct(ApiResponse $apiResponse)
    {
        $this->languages = Language::all();
        $this->txnflag = new SubscriptionController;
        $this->apiResponse=$apiResponse;
    }

    public function get_opp($slug)
    {
        try { 
            $opportunity = Opportunity::where('slug', $slug)->firstOrFail();
            $flag = 0;
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $flag = $this->txnflag->check_subscription(Auth::user()->id);
                $plus_status = PlusTransaction::where('user_id', $user->id)->where('opportunity_id', $opportunity->id)->select('status');
                $data = array('opportunity' => $opportunity, 'languages' => $this->languages, 'txnflag' => $flag, 'plus_status' => $plus_status);
                return $this->apiResponse->sendResponse(200,'Success',$data);
            };
             
        } catch (Exception $e) {
            //abort(404);
            return $this->apiResponse->sendResponse(500,'Internal Server Error','Internal Server Error');
        } 
        
    }
}
