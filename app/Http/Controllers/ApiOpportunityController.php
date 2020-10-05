<?php

namespace App\Http\Controllers;

use App\Language;
use App\Opportunity;
use App\Tag;
use App\User;
use App\UserViewedOpportunity;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function get_opp_by_slug($slug)
    {
        try {
            $hyphen_index = strrpos($slug, "-");
            $legacy_id = substr($slug, $hyphen_index + 1);
            if (preg_match("/[a-z]/i", $legacy_id)) {
                $opportunity = Opportunity::with(['location', 'fund_type', 'opportunity_translations' => function ($query) {
                    $query->where('locale', 'en');
                }])->where('slug', $slug)->firstOrFail();
            } else {
                $phoenix_opp_id = DB::table('legacy_opportunities')->where('legacy_opportunity_id', $legacy_id)->pluck('phoenix_opportunity_id');
                if (count($phoenix_opp_id) == 1)
                    $phoenix_opp_id = $phoenix_opp_id[0];
                else {
                    return $this->apiResponse->sendResponse(404, 'Opportunity not found', null);
                }
                $opportunity = Opportunity::with(['location', 'fund_type', 'opportunity_translations' => function ($query) {
                    $query->where('locale', 'en');
                }])->where('slug', 'LIKE', '%' . substr($slug, 0, strrpos($slug, "-")) . '%')->where('id', $phoenix_opp_id)->firstOrFail();
            }

            $opportunity_next = Opportunity::where('id', '>', $opportunity["id"])->select('slug')->first();
            if ($opportunity_next != null)
                $opportunity["next_slug"] = $opportunity_next["slug"];

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

                    $opportunity_next = Opportunity::where('id', '<', $opportunity["id"])->select('slug')->orderByDesc('id')->first();
                    $opportunity["next_slug"] = $opportunity_next["slug"];

                    return $this->apiResponse->sendResponse(200, 'Success', $opportunity);
                }

                return $this->apiResponse->sendResponse(200, 'No past opportunities available', null);
            }

            return $this->apiResponse->sendResponse(401, 'User unauthenticated', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getTraceAsString());
        }
    }

    public function get_next_opportunity(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                // $user = User::find($request->user_id)->get();
                $current_opp_id = Opportunity::select('id')->where('slug', $request->slug)->first();
                if (!is_null($current_opp_id)) {
                    $opportunity = Opportunity::with(['location', 'fund_type', 'opportunity_translations' => function ($query) use ($current_opp_id, $request) {
                        $query->where('locale', 'en');
                    }])->where('id', '>', $current_opp_id["id"])->whereHas('tags', function ($query) use ($user) {
                        $query->whereIn('tags.id', $user->tags);
                    })->first();

                    $opportunity_next = Opportunity::where('id', '>', $opportunity["id"])->select('slug')->first();
                    $opportunity["next_slug"] = $opportunity_next["slug"];

                    return $this->apiResponse->sendResponse(200, 'Success', $opportunity);
                }

                return $this->apiResponse->sendResponse(200, 'No new opportunities available', null);
            }

            return $this->apiResponse->sendResponse(401, 'User unauthenticated', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getTraceAsString());
        }
    }

    public function get_new_opportunities()
    {
        try {
            $user = Auth::user();

            $gopportunities = Opportunity::with(['new_location', 'fund_type', 'opportunity_translations' => function ($query) {
                $query->where('locale', 'en');
            }, 'tags' => function ($query) {
                $query->select('id', 'tag');
            }, 'relevance'])->where('deadline', '>', Carbon::now())->whereHas('tags', function ($query) use ($user) {
                $query->whereNotIn('tags.id', $user->tags);
            });

            // return $user->tags;
            $opportunities = Opportunity::with(['new_location', 'fund_type', 'opportunity_translations' => function ($query) {
                $query->where('locale', 'en');
            }, 'tags' => function ($query) {
                $query->select('id', 'tag');
            }, 'relevance'])->where('deadline', '>', Carbon::now())->whereHas('tags', function ($query) use ($user) {
                $query->whereIn('tags.id', $user->tags);
            })->union($gopportunities)->paginate(10);

            if (count($user->saved_opportunities) > 0) {
                $subset_saved_opporutnies = $user->saved_opportunities->map->only('id')->toArray();
                foreach ($opportunities as $opportunity) {
                    if (in_array(["id" => $opportunity->id], $subset_saved_opporutnies))
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
        } catch (Exception $e) {
            // return $this->apiResponse->sendResponse(200, "Successfully retrieved opportunities", null);
            // abort(404);
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    public function get_opportunities()
    {
        try {
            $user = Auth::user();

//            $gopportunities = Opportunity::with(['location', 'fund_type', 'opportunity_translations' => function ($query) {
//                $query->where('locale', 'en');
//            }, 'tags' => function ($query){
//                $query->select('id', 'tag');
//            }, 'raw_relevance' => function ($query){
//                $query->select('score');
//            }])->where('deadline', '>', Carbon::now())->whereHas('tags', function ($query) use ($user) {
//                $query->whereNotIn('tags.id', $user->tags);
//            });

            Opportunity::with(['tags' => function ($query) use ($user) {
                $query->whereIn('tags.id', $user->tags()->pluck('id')->toArray());
            }])->where('deadline', '>', Carbon::now());


            // return $user->tags;
            $opportunities = Opportunity::with(['location', 'fund_type', 'opportunity_translations' => function ($query) {
                $query->where('locale', 'en');
            }, 'tags' => function ($query) use ($user) {
                $query->whereIn('tags.id', $user->tags()->pluck('id')->toArray())->select('id', 'tag');
            }, 'raw_relevance' => function ($query) {
                $query->select('score');
            }])->where('deadline', '>', Carbon::now())->whereHas('tags', function ($query) use ($user) {
                $query->whereIn('tags.id', $user->tags);
            })->paginate(10);

            if (count($user->saved_opportunities) > 0) {
                $subset_saved_opporutnies = $user->saved_opportunities->map->only('id')->toArray();
            } else {
                $subset_saved_opporutnies = [];
            }

            foreach ($opportunities as $opportunity) {
                if (in_array(["id" => $opportunity->id], $subset_saved_opporutnies))
                    $opportunity['saved'] = 1;
                else
                    $opportunity['saved'] = 0;

                if (is_null($opportunity["raw_relevance"]))
                    $opportunity["relevance"] = 0;
                else
                    $opportunity["relevance"] = $opportunity["raw_relevance"]["score"];
            }

            return $this->apiResponse->sendResponse(200, "Successfully retrieved opportunities", $opportunities);
        } catch (Exception $e) {
            // return $this->apiResponse->sendResponse(200, "Successfully retrieved opportunities", null);
            // abort(404);
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

    public function get_opportunity_stack()
    {
        $tags = Tag::where('tag_type_id', 1)->get();
        $opportunities = array();
        foreach ($tags as $tag) {
            $opportunity = $tag->opportunities()->with(['location', 'fund_type', 'tags' => function ($query) {
                $query->select('id', 'tag');
            }])->latest()->first();

            if (!is_null($opportunity))
                $opportunities[$opportunity->id] = $opportunity;

            if (count($opportunities) == 6)
                break;
        }

        return $this->apiResponse->sendResponse(200, "Successfully retrieved opportunities", array_values($opportunities));
    }

    public function save_user_views_opp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'opp_ids' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'No Opportunity ids were given', $validator->errors());
            }
            foreach ($request->opp_ids as $opp) {
                $saved_user_views_opp = UserViewedOpportunity::where('user_id', Auth::user()->id)->where('opportunity_id', $opp)->first();
                if (is_null($saved_user_views_opp)) {
                    $viewed_opp = new UserViewedOpportunity();
                    $viewed_opp->user_id = Auth::user()->id;
                    $viewed_opp->opportunity_id = $opp;
                    $viewed_opp->save();
                }
            }

            return $this->apiResponse->sendResponse(200, 'User View opportunity added', null);

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error 3.', $e->getMessage());
        }
    }

    public function get_user_views_opp()
    {
        try {

            $opportunities = UserViewedOpportunity::where('user_id', Auth::user()->id)->get();
            if (count($opportunities) > 0)
                return $this->apiResponse->sendResponse(200, 'Success', $opportunities);

            return $this->apiResponse->sendResponse(404, 'There is no user saved opportunity', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
