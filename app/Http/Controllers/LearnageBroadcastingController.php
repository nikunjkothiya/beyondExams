<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\UserLive;
use Exception;
use Auth;

class LearnageBroadcastingController extends Controller
{
    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function get_live_mentors(){
        try {
            $live_users = UserLive::where('live', 1)->get();
            if(count($live_users) == 0)
                return $this->apiResponse->sendResponse(404, 'No user is broadcasting.', null);

            return $this->apiResponse->sendResponse(200, 'Success', $live_users);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTrace());
        }
    }

    public function add_live_mentor(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'peer_id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }
            $live_user = new UserLive();
            $live_user->user_id = Auth::user()->id;
            $live_user->peer_id = $request->peer_id;
            $live_user->save();
            return $this->apiResponse->sendResponse(200, 'User is now broadcasting live.', $live_user);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTrace());
        }
    }

    public function update_live_mentor(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }
            $live_user = UserLive::where('id', $request->id)->first();
            if(is_null($live_user))
                return $this->apiResponse->sendResponse(404, 'No broadcasting session found with given id.', null);
            if(isset($request->peer_id))
                $live_user->peer_id = $request->peer_id;
            if(isset($request->name))
                $live_user->name = $request->name;
            if(isset($request->live))
                $live_user->live = $request->live;
            $live_user->save();
            return $this->apiResponse->sendResponse(200, 'Broadcasting details updated.', $live_user);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTrace());
        }
    }
}
