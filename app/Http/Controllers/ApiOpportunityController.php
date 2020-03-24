<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use App\Language;
use App\Opportunity;
use App\PlusTransaction;
use Validator;
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
            return $this->apiResponse->sendResponse(500,'Internal Server Error',null);
        } 
        
    }

    public function get_opp_by_tags(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
	            'page' => 'required',
	        ]);

	        if($validator->fails()){
	        	return $this->apiResponse->sendResponse(400,$validator->errors(),null);
	        }
            
            $flag = 0;
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $tag_id_query = DB::table('tag_user')->select('tag_id')->where('user_id',$user->id)->orderBy('tag_id')->get();
                $tag_id_json_array = json_decode($tag_id_query, true);
                $tag_ids = array();
                foreach ($tag_id_json_array as $tag){
                    $tag_ids[]=$tag['tag_id'];}
                
                $opp_id_json = DB::table('opportunity_tag')->select('opportunity_id')->whereIn('tag_id',$tag_ids )->take(3)->skip(($request->page)*3)->get();
                $opp_id_json_array = json_decode($opp_id_json, true);
                $opp_ids = array();
                foreach ($opp_id_json_array as $opp){
                    $opp_ids[]=$opp['opportunity_id'];}
                
                $opp_slug_json = DB::table('opportunities')->select('slug')->whereIn('id',$opp_ids)->get();
                $opp_slug_json_array = json_decode($opp_slug_json, true);
                $opp_slugs = array();
                $i=0;
                foreach ($opp_slug_json_array as $opp_slug){
                    $opp_slugs[]=array('slug'=>$opp_slug['slug'], 'id'=>$opp_ids[$i]);$i=$i+1;}
                
                return $this->apiResponse->sendResponse(200,'Success',$opp_slugs);               
                
            };
             
        } catch (Exception $e) {
            //abort(404);
            return $this->apiResponse->sendResponse(500,'Internal Server Error',null);
        }        
        
    }
}
