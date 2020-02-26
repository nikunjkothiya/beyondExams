<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use App\Language;
use App\Opportunity;
use App\PlusTransaction;
use App\User;
use Auth;
use DB;

class ApiOpportunityController extends Controller
{
    protected $languages;
    private $apiResponse;
    
    public function __construct(ApiResponse $apiResponse)
    {
        $this->languages = Language::all();
        //$this->txnflag = new SubscriptionController;
        $this->apiResponse=$apiResponse;
    }

    public function get_opp($slug)
    {
        try { 
            //$flag = 0;
            //if (Auth::check()) {
            //    $user = User::find(Auth::user()->id);
                // $flag = $this->txnflag->check_subscription(Auth::user()->id);
                // $plus_status = PlusTransaction::where('user_id', $user->id)->where('opportunity_id', $opportunity->id)->select('status');
                $opportunity = Opportunity::where('slug', $slug)->firstOrFail();
                $data = array('opportunity' => $opportunity, 'languages' => $this->languages);//, 'txnflag' => $flag, 'plus_status' => $plus_status);
                return $this->apiResponse->sendResponse(200,'Success',$data);
            //};
             
        } catch (Exception $e) {
            //abort(404);
            return $this->apiResponse->sendResponse(500,'Internal Server Error','Internal Server Error');
        } 
        
    }

    public function get_opp_by_tags(Request $request)
    {
        /*try {
            
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
        }*/
        $tag_id_query = DB::table('tag_user')->select('tag_id')->where('user_id',$request->user_id)->get();//Auth::user()->id)->get();
        $tag_id_json_array = json_decode($tag_id_query, true);
        $tag_ids = array();
        foreach ($tag_id_json_array as $tag){
            $tag_ids[]=$tag['tag_id'];}

        $opp_id_json = DB::table('opportunity_tag')->select('opportunity_id')->whereIn('tag_id',[1,2,3] )->get();
        $opp_id_json_array = json_decode($opp_id_json, true);
        $opp_ids = array();
        foreach ($opp_id_json_array as $opp){
            $opp_ids[]=$opp['opportunity_id'];}

        
        return $this->apiResponse->sendResponse(200,'Success',$opp_ids);
        
        
    }
}
