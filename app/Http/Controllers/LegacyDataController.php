<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponse;
use App\PremiumValidity;
use App\User;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Exception;

class LegacyDataController extends Controller
{

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function insert_legacy_users(request $request)
    {
        try {
            if (!isset($request->users)) {
                return $this->apiResponse->sendResponse(400, "No data given", null);
            }
            foreach($request->users as $user){
                $insertUser = new User();
                $insertUser->email = $user->email;
                $insertUser->unique_id = $user->user_id;
                $insertUser->name = $user->user_name;
                $insertUser->save();
                $insertUser->role()->create(
                    ['is_user' => 1]
                );
            }
            return $this->apiResponse->sendResponse(200, 'Operation Successful', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function insert_legacy_subscriptions(request $request)
    {
        try {
            if (!isset($request->subscriptions)) {
                return $this->apiResponse->sendResponse(400, "No data given", null);
            }
            $invalidUsers = array();
            foreach($request->subscriptions as $subscription){
                $user = User::where('email', $subscription->email_id)->first();
                if(is_null($user)){
                    array_push($invalidUsers, $subscription);
                } else {
                    $insertPlan = new PremiumValidity();
                    $insertPlan->user_id = $user->id;   
                    $insertPlan->end_date = Carbon::parse($subscription->end_date);
                    $insertPlan->save();
                }
            }
            return $this->apiResponse->sendResponse(200, 'Operation Successful', $invalidUsers);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
