<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\UserFollower;
use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;

class SocialController extends Controller
{
    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }
    public function checkAuthentication(){
	try {
	    if (Auth::check())
                return $this->apiResponse->sendResponse(200, 'Success', "User authenticated");
	    return $this->apiResponse->sendResponse(401, 'Failed', "Auth check failed");
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_followers() {
        try {
            // $follower_ids  = User::find($request->uid)->followers()->pluck('influencer_id');
            $follower_ids  = Auth::user()->followers()->pluck('influencer_id');
            $followers = User::whereIn('id', $follower_ids)->get(['id', 'name', 'avatar']);

            return $this->apiResponse->sendResponse(200, 'Success', $followers);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_influencers() {
        try {
            // $follower_ids  = User::find($request->uid)->influencers()->pluck('influencer_id');
            $follower_ids  = Auth::user()->influencers()->pluck('influencer_id');
            $followers = User::whereIn('id', $follower_ids)->get(['id', 'name', 'avatar']);
            
            return $this->apiResponse->sendResponse(200, 'Success', $followers);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function start_following(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
    
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Id is required to follow', $validator->errors());
            }
            if(is_null(User::find($request->id)))
                return $this->apiResponse->sendResponse(404, 'No such user exist', null);

            $follow = new UserFollower();
            $follow->user_id = $request->uid;
            // $follow->user_id = Auth::user()->id;
            $follow->influencer_id = $request->id;
            $follow->save();
            return $this->apiResponse->sendResponse(200, 'Success', $follow);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
