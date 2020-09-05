<?php

namespace App\Http\Controllers;

use App\Language;
use App\Opportunity;
use App\OpportunityRelevance;
use App\PlusTransaction;
use App\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OpportunityController extends Controller
{
    protected $languages;
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse, SubscriptionController $subscriptionController)
    {
        $this->languages = Language::all();
        $this->txnflag = $subscriptionController;
        $this->apiResponse = $apiResponse;
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
            };
        } catch (Exception $e) {
            abort(404);
        }
        return view('pages.opportunity', ['opportunity' => $opportunity, 'languages' => $this->languages, 'txnflag' => $flag, 'plus_status' => $plus_status]);
    }

    public function mark_relevant(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:opportunities',
                'score' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::check()) {
                $user = Auth::user();
                $opp_id = $request->id;
//                $check = DB::table('opportunity_relevance')->select('opportunity_id')->where('user_id', $user->id)->first();
                $opportunity_relevance = OpportunityRelevance::where('user_id', $user->id)->where('opportunity_id', $opp_id)->first();
                if ($opportunity_relevance) {
                    $opportunity_relevance->score = $request->score;
                    $opportunity_relevance->save();
                } else {
                    $opportunity_relevance = new OpportunityRelevance();
                    $opportunity_relevance->user_id = $user->id;
                    $opportunity_relevance->opportunity_id = $opp_id;
                    $opportunity_relevance->score = $request->score;
                    $opportunity_relevance->save();
                }

                return $this->apiResponse->sendResponse(200, 'Relevance saved', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthenticated', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
