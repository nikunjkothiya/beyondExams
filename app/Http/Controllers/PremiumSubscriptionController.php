<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use Auth;
use App\Currency;
use App\PremiumPlan;
use App\PremiumValidity;
use App\Transaction;
use Carbon\Carbon;
use Razorpay\Api\Api;


class PremiumSubscriptionController extends Controller
{
    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    function list_premium_plans()
    {
        try {
            $plans = PremiumPlan::where('enabled', 1)->get();
            foreach ($plans as $plan) {
                $plan['currency'] = Currency::where('id', $plan->currency_id)->first()->name;
                unset($plan['currency_id']);
                unset($plan['updated_at']);
                unset($plan['created_at']);
            }
            return $this->apiResponse->sendResponse(200, 'Success', $plans);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    function get_subscriptions()
    {
        try {
            $plan = PremiumValidity::where('user_id', Auth::user()->id)->first();
            if ($plan) {
                $result['purchased'] = true;
                $result['end_date'] = $plan->end_date;
                if ($plan->end_date < Carbon::now()) {
                    $result['active'] = false;
                } else {
                    $result['active'] = true;
                }
                return $this->apiResponse->sendResponse(200, 'Premium Plan Info', $result);
            } else {
                $result['purchased'] = false;
                $result['active'] = false;
                $result['end_date'] = null;
                return $this->apiResponse->sendResponse(200, 'No Premium Plan Bought', $result);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    function add_days_to_premium(request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $plan = PremiumValidity::where('user_id', Auth::user()->id)->first();
            if ($plan) {
                $plan->end_date = Carbon::parse($plan->end_date)->addDays($request->days);
                $plan->save();
                return $this->apiResponse->sendResponse(200, 'Days added to plan.', $plan);
            }
            return $this->apiResponse->sendResponse(400, 'User has no active plan', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    function premium_checkout(request $request)
    {
        $validator = Validator::make($request->all(), [
            "payment_id"  => "required",
            "plan_id" => "required"
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {

            // Set Variables
            $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));
            $plan = PremiumPlan::where('id', $request->plan_id)->first();

            // Get Payment Details
            $payment = $api->payment->fetch($request->payment_id);

            // Capture the payment
            if ($payment->status == 'authorized') {
                // Capturing Payment
                $payment->capture(
                    array('amount' => $payment->amount, 'currency' => $payment->currency)
                );
                // Create A TXN
                $txn = new Transaction();
                $txn->transaction_id = $payment->id;
                $txn->user_id = Auth::user()->id;
                $txn->product_id = 1;
                $txn->valid = 1;
                $txn->save();

                // Add Plan to user Account
                $user_validity = PremiumValidity::where('user_id', Auth::user()->id)->first();
                if ($user_validity) {
                    $user_validity->end_date = Carbon::parse($user_validity->end_date)->addMonths($plan->months);
                    $user_validity->save();
                } else {
                    $user_validity = new PremiumValidity();
                    $user_validity->user_id = Auth::user()->id;
                    $user_validity->end_date = Carbon::now()->addMonths($plan->months);
                    $user_validity->save();
                }
                return $this->apiResponse->sendResponse(200, 'Order Created', null);
            } else if ($payment->status == 'refunded') {
                // Payment was refunded
                return $this->apiResponse->sendResponse(400, 'Transaction was refunded', null);
            } else if ($payment->status == 'failed') {
                // Payment Failed
                return $this->apiResponse->sendResponse(400, 'Transaction was failed', null);
            } else if ($payment->status == 'captured') {
                // Payment Token Already used
                return $this->apiResponse->sendResponse(400, 'Transaction was already captured', null);
            } else {
                // Unkown Error
                return $this->apiResponse->sendResponse(400, 'Transaction not captured', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }
}
