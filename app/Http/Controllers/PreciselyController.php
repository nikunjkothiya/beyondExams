<?php

namespace App\Http\Controllers;

use App\Country;
use App\StudentDetail;
use App\Tag;
use App\User;
use App\UserDetail;
use Auth;
use Config;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @property ApiResponse apiResponse
 */
class PreciselyController extends Controller
{
    protected $txnflag;
    private $base_url = 'https://precisely-test1221001-dev.s3.ap-south-1.amazonaws.com/';

    public function __construct(ApiResponse $apiResponse)
    {
        $this->msg = "";
        $this->apiResponse = $apiResponse;
        $this->txnflag = new SubscriptionController($apiResponse);
    }

    public function get_language(Request $request)
    {
        try {
            $languages = DB::table('languages')->select('id', 'code', 'language', 'language_example')->get();

            return $this->apiResponse->sendResponse(200, 'All languages fetched successfully', $languages);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }


    public function get_filters(Request $request)
    {
        try {
            $filters = Tag::all();

            return $this->apiResponse->sendResponse(200, 'All Tags fetched successfully', $filters);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    public
    function save_user_language(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:languages',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }


        if (Auth::check()) {
            $user = User::find(Auth::user()->id);

            try {
                $pcheck = UserDetail::where('user_id', $user->id)->first();
            } catch (Exception $e) {
                return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
            }

            if ($pcheck) {
                $pcheck->language_id = $request->id;
                $pcheck->save();
                // Flags
                $flag = 2;
                $check_detail = StudentDetail::where('user_id', $user->id)->first();
                $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user->id)->first();
                if ($check_detail) {
                    // Check User Details is filled
                    if ($check_tag) {
                        // If Category is filled
                        $flag = 0;
                    } else {
                        // If Category is not filled
                        $flag = 3;
                    }
                    $check_detail->flag = $flag;
                    $check_detail->save();
                } else {
                    // Check User Details is not filled
                    $flag = 2;
                }
                $responseArray = [
                    'new' => $flag
                ];
                return $this->apiResponse->sendResponse(200, 'Success', $responseArray);
            } else {
                $record = new UserDetail;
                $record->user_id = $user->id;
                $record->language_id = $request->id;
                $record->save();
                // Flags
                $flag = 2;
                $check_detail = StudentDetail::where('user_id', $user->id)->first();
                $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user->id)->first();
                if ($check_detail) {
                    if ($check_tag) {
                        // If Category is filled
                        $flag = 0;
                    } else {
                        // If Category is not filled
                        $flag = 3;
                    }
                    $check_detail->flag = $flag;
                    $check_detail->save();
                } else {
                    $flag = 2;
                }
                $responseArray = [
                    'new' => $flag
                ];
                return $this->apiResponse->sendResponse(200, 'Success', $responseArray);
            }
        } else {
            return $this->apiResponse->sendResponse(500, 'Users not logged in', null);
        }
    }

    public
    function save_user_filters(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
            } else {
                return $this->apiResponse->sendResponse(500, 'Un Authorized', null);
            }

            $user = User::where('id', $user->id)->first();
            $tags = $request->tags;
            // return $this->apiResponse->sendResponse(200, 'Saved filters selected by user', $tags);
            if (empty($tags)) {
                return $this->apiResponse->sendResponse(400, 'Select at least one filter', null);
            }

            // Read $tags as json
            $user->tags()->sync(json_decode($tags));
            // Check if profile are filled if filled then 0 else 2
            $check_detail = StudentDetail::where('user_id', $user->id)->first();
            if ($check_detail) {
                $flag = 0;
                $check_detail->flag = $flag;
                $check_detail->save();
            } else {
                $flag = 2;
            }
            $responseArray = [
                'new' => $flag
            ];
            return $this->apiResponse->sendResponse(200, 'Saved filters selected by user', $responseArray);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public
    function get_all_countries(Request $request)
    {
        $countries = Country::all();
        return $this->apiResponse->sendResponse(200, 'All countries fetched.', $countries);
    }

    public
    function get_location($location_id)
    {
        try {
            $country = DB::table('opportunity_locations')->select('location')->where('id', $location_id)->get();
            return $this->apiResponse->sendResponse(200, 'Success', $country);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public
    function get_user_language()
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
            } else {
                return $this->apiResponse->sendResponse(500, 'Un Authorized', null);
            }

            $lang = DB::table('user_details')->select('language_id')->where('user_id', $user->id)->get();
            return $this->apiResponse->sendResponse(200, 'Success', $lang);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public
    function get_user_filters()
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthenticated', null);
            }

            $tags_json = DB::table('tag_user')->select('tag_id')->where('user_id', $user->id)->get();
            $tags = [];
            foreach ($tags_json as $tag) {
                $tags[] = $tag;
            }
            $tags_processed = [];
            foreach ($tags as $tag) {
                $tags_processed[] = $tag->tag_id;
            }

            return $this->apiResponse->sendResponse(200, 'Success', $tags_processed);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    function razorpay_demo_checkout(request $request)
    {
        $validator = Validator::make($request->all(), [
            "payment_id" => "required",
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {

            // Set Variables
            $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));

            // Get Payment Details
            $payment = $api->payment->fetch($request->payment_id);

//            $mentor_detail = MentorDetail::where('user_id', $request->mentor_id)->first();
//
//            if (!$mentor_detail) {
//                return $this->apiResponse->sendResponse(400, 'Mentor does not exist', null);
//            }

            if (!$payment) {
                return $this->apiResponse->sendResponse(400, 'Payment ID is invalid', null);
            }

            // Capture the payment
            if ($payment->status == 'authorized') {

                // Capturing Payment
                $payment->capture(
                    array('amount' => $payment->amount, 'currency' => $payment->currency)
                );
                // Create A TXN
//                $txn = new Transaction();
//                $txn->transaction_id = $payment->id;
//                $txn->user_id = Auth::id();
//                $txn->mentor_id = $request->mentor_id;
//                $txn->product_id = 3;
//                $txn->valid = 1;
//                $txn->save();


//                $mentor_detail->num_paid_subscribers = $mentor_detail->num_paid_subscribers + 1;
//                $mentor_detail->save();

                return $this->apiResponse->sendResponse(200, 'Purchase Successful.', null);
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
            return $this->apiResponse->sendResponse(400, 'Payment Error', $e->getMessage());
        }
    }

}
