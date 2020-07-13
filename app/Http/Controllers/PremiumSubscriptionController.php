<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use App\PremiumPlan;
use App\PremiumTxn;
use App\User;

class PremiumSubscriptionController extends Controller
{
    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    function list_all_plans(){
        try {
            $plans = PremiumPlan::all();
            return $this->apiResponse->sendResponse(200, 'Success', $plans);
        }  catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    function get_subscriptions(){
        return $this->apiResponse->sendResponse(200, 'Success', []);
        try {
            $user = User::where('user_id',Auth::user()->id)->first();

            return $this->apiResponse->sendResponse(200, 'Success', $user);
        }  catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

}
