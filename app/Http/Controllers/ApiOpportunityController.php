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
        $this->apiResponse = $apiResponse;
    }

    public function get_opp($slug)
    {
        try {
            $opportunity = Opportunity::with(['location', 'fund_type', 'opportunity_translations' => function ($query) {
                $query->where('locale', 'en');
            }])->where('slug', $slug)->firstOrFail();

            return $this->apiResponse->sendResponse(200, 'Success', $opportunity);
        } catch (Exception $e) {
            //abort(404);
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', null);
        }
    }

    public function get_previous_opportunity(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $current_opp_id = Opportunity::select('id')->where('slug', $request->slug)->first();
                if (!is_null($current_opp_id)) {
                    $opportunity = Opportunity::with(['location', 'fund_type', 'opportunity_translations' => function ($query) use ($current_opp_id, $request) {
                        $query->where('locale', 'en');
                    }])->where('id', '<', $current_opp_id["id"])->whereHas('tags', function ($query) use ($user) {
                        $query->whereIn('tags.id', $user->tags);
                    })->orderByDesc('id')->first();

                    return $this->apiResponse->sendResponse(200, 'Success', $opportunity);
                }

                return $this->apiResponse->sendResponse(200, 'No past opportunities available', null);
            }

            return $this->apiResponse->sendResponse(401, 'User unauthenticated', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getTrace());
        }
    }

    public function get_next_opportunity(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
//            $user = User::find($request->user_id)->get();
                $current_opp_id = Opportunity::select('id')->where('slug', $request->slug)->first();
                if (!is_null($current_opp_id)) {
                    $opportunity = Opportunity::with(['location', 'fund_type', 'opportunity_translations' => function ($query) use ($current_opp_id, $request) {
                        $query->where('locale', 'en');
                    }])->where('id', '>', $current_opp_id["id"])->whereHas('tags', function ($query) use ($user) {
                        $query->whereIn('tags.id', $user->tags);
                    })->first();

                    return $this->apiResponse->sendResponse(200, 'Success', $opportunity);
                }

                return $this->apiResponse->sendResponse(200, 'No new opportunities available', null);
            }

            return $this->apiResponse->sendResponse(401, 'User unauthenticated', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getTrace());
        }
    }

    public function get_opportunities(Request $request)
    {
        try {
//            $user_id = $request->user_id;

//            if (!is_null(User::find($user_id)->id)) {
            if (Auth::check()) {
                $user = Auth::user();
//                $user = User::find($user_id);
//            $tags = $user->tags;
                $opportunities = Opportunity::with(['location', 'fund_type', 'opportunity_translations' => function ($query) {
                    $query->where('locale', 'en');
                }])->whereHas('tags', function ($query) use ($user) {
                    $query->whereIn('tags.id', $user->tags);
                })->paginate(2);

                if (count($user->saved_opportunities) > 0) {
                    foreach ($opportunities as $opportunity) {
                        if (in_array($opportunity->id, $user->saved_opportunities))
                            $opportunity['saved'] = 1;
                        else
                            $opportunity['saved'] = 0;
                    }
                } else {
                    foreach ($opportunities as $opportunity) {
                        $opportunity['saved'] = 0;
                    }
                }

                return $this->apiResponse->sendResponse(200, "Successfully retrieved opportunities", $opportunities);
            } else {
                return $this->apiResponse->sendResponse(500, 'Users not logged in', null);
            }
        } catch (Exception $e) {
//    return $this->apiResponse->sendResponse(200, "Successfully retrieved opportunities", null);
            //abort(404);
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    public function get_opp_by_tags(Request $request)
    {
        /*
         * Get tag_ids saved by user
         * Select opp_id with tags in tag_ids
         * Get slug for opp_ids
         * Get save status of opp_ids
         * Get description and heading of locale_english
         * Get country from country_code
         *
         */
        try {

            $validator = Validator::make($request->all(), [
                'page' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, $validator->errors(), null);
            }

            $flag = 0;
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $tag_id_query = DB::table('tag_user')->select('tag_id')->where('user_id', $user->id)->orderBy('tag_id')->get();
                $tag_id_json_array = json_decode($tag_id_query, true);
                $tag_ids = array();
                foreach ($tag_id_json_array as $tag) {
                    $tag_ids[] = $tag['tag_id'];
                }
                $tag_ids = array_unique($tag_ids);

                $opp_id_json = DB::table('opportunity_tag')->select('opportunity_id')->whereIn('tag_id', $tag_ids)->orderBy('opportunity_id')->distinct()->take(10)->skip(($request->page) * 10)->get();
                $opp_id_json_array = json_decode($opp_id_json, true);
                $opp_ids = array();
                foreach ($opp_id_json_array as $opp) {
                    $opp_ids[] = $opp['opportunity_id'];
                }
                $opp_ids = array_unique($opp_ids);

                $opp_slug_json = DB::table('opportunities')->select('slug')->whereIn('id', $opp_ids)->get();
                $opp_slug_json_array = json_decode($opp_slug_json, true);
                $opp_slugs = array();

                $saved_opp = DB::table('opportunity_user')->select('opportunity_id')->where('user_id', $user->id)->get();
                $saved_opp_json_array = json_decode($saved_opp, true);
                $saved_opp_ids = array();
                foreach ($saved_opp_json_array as $sa_opp) {
                    $saved_opp_ids[] = $sa_opp['opportunity_id'];
                }


                $i = 0;
                foreach ($opp_slug_json_array as $opp_slug) {
                    if (in_array($opp_ids[$i], $saved_opp_ids)) {
                        $desc = DB::table('opportunities')->select('*')->where('slug', $opp_slug['slug'])->first();
                        $cont = DB::table('countries')->select('name')->where('id', $desc->opportunity_location_id)->first();
                        $desc->opportunity_location = $cont;
                        $main = DB::table('opportunity_translations')->select('*')->where([['opportunity_id', $opp_ids[$i]], ['locale', 'en']])->first();
                        $opp_slugs[] = array('slug' => $opp_slug['slug'], 'id' => $opp_ids[$i], 'main' => $main, 'desc' => $desc, 'saved' => 1);
                        $i = $i + 1;
                    } else {
                        $desc = DB::table('opportunities')->select('*')->where('slug', $opp_slug['slug'])->first();
                        $cont = DB::table('countries')->select('name')->where('id', $desc->opportunity_location_id)->first();
                        $desc->opportunity_location = $cont;
                        $main = DB::table('opportunity_translations')->select('*')->where([['opportunity_id', $opp_ids[$i]], ['locale', 'en']])->first();
                        $opp_slugs[] = array('slug' => $opp_slug['slug'], 'id' => $opp_ids[$i], 'main' => $main, 'desc' => $desc, 'saved' => 0);
                        $i = $i + 1;
                    }
                }

                return $this->apiResponse->sendResponse(200, 'Success', $opp_slugs);

            };

        } catch (Exception $e) {
            //abort(404);
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', null);
        }

    }
}
