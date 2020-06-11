<?php

namespace App\Http\Controllers;

use App\Action;
use App\ActionProperty;
use App\ActionUser;
use App\ActionUserOpportunity;
use App\Analytics;
use App\Country;
use App\Discipline;
use App\Language;
use App\Opportunity;
use App\Qualification;
use App\Tag;
use App\User;
use App\UserDetail;
use Carbon\Carbon;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ApiResponse;
use Auth;

/**
 * @property ApiResponse apiResponse
 */
class PreciselyController extends Controller
{
    protected $txnflag;

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

    public function submit_profile(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $user_id = $user->id;
                $validator = Validator::make($request->all(), [
                    'firstname' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'college' => 'required|string|max:1024',
                    'gpa' => 'required|numeric|between:0,10.00',
                    'qualification' => 'required|integer|min:1|max:' . Qualification::count(),
                    'discipline' => 'required|integer|min:1|max:' . Discipline::count(),
                    'city' => 'required|string|max:255',
                    'country' => 'required|integer|min:1|max:' . Country::count(),
                    'email' => 'required|email',
                ]);
//            dd($validator);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                //$user_id = $request->user_id;

                $check = UserDetail::where('user_id', $user_id)->first();

                if (is_null($check)) {
                    $record = new UserDetail;
                    $record->user_id = $user_id;
                    $record->language_id = Language::where('code', Config::get('app.locale'))->first()->id;
                    $record->email = $request->email;
                    $record->firstname = $request->firstname;
                    $record->lastname = $request->lastname;
                    $record->college = $request->college;
                    $record->city = $request->city;
                    $record->gpa = $request->gpa;
                    $record->qualification_id = $request->qualification;
                    $record->discipline_id = $request->discipline;
                    $record->country_id = $request->country;
                    $record->save();
                    if ($record) {
                        return $this->apiResponse->sendResponse(200, 'User details saved.', $record);
                    } else {
                        return $this->apiResponse->sendResponse(500, 'Internal server error. New record could not be inserted', null);
                    }
                } else {
                    $check->email = $request->email;
                    $check->firstname = $request->firstname;
                    $check->lastname = $request->lastname;
                    $check->college = $request->college;
                    $check->city = $request->city;
                    $check->gpa = $request->gpa;
                    $check->qualification_id = $request->qualification;
                    $check->discipline_id = $request->discipline;
                    $check->country_id = $request->country;
                    $check->save();
                    if ($check) {
                        return $this->apiResponse->sendResponse(200, 'User details saved.', $check);
                    } else {
                        return $this->apiResponse->sendResponse(500, 'Internal server error. Record could not be updated', null);
                    }
                }
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error 3.', $e->getMessage());
        }
    }

//    TODO: CORRECT RETURN TYPE
    public function get_profile(Request $request)
    {
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            try {
                $pcheck = UserDetail::where('user_id', $user->id)->first();
            } catch (Exception $e) {
                return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
            }

            if ($pcheck) {
                $countries = Country::all();
                $disciplines = Discipline::all();
                $qualifications = Qualification::all();
                $data['user_details'] = $pcheck;
                $avatar = DB::table('users')->select('avatar')->where('id', $user->id)->get();
                foreach ($avatar as $ava) {
                    $data['avatar'] = $ava->avatar;
                    break;
                }
                #$data['txnflag']=$this->txnflag->check_subscription($request->user_id);

                return $this->apiResponse->sendResponse(200, 'Successfully fetched user profile.', $data);
            }
        } else {
            return $this->apiResponse->sendResponse(500, 'Users not logged in', null);
        }

    }

    public function save_opportunity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:opportunities',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                try {
                    $opp_id = $request->id;
                    $user = User::where('id', $user->id)->first();
                    $check = DB::table('opportunity_user')->select('opportunity_id')->where('user_id', $user->id)->get();
                    if ($check == null) {
                        DB::table('opportunity_user')->insert(['opportunity_id' => $opp_id, 'user_id' => $user->id]);
                    } else {
                        $flag = 0;
                        foreach ($check as $c) {
                            if ($c->opportunity_id == $opp_id) {
                                $flag = 1;
                                break;
                            }
                        }
                        if ($flag == 0) {
                            DB::table('opportunity_user')->insert(['opportunity_id' => $opp_id, 'user_id' => $user->id]);
                        }
                    }

                } catch (Exception $e) {
                    return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
                }

            } else {
                $this->apiResponse->sendResponse(400, 'Not Authorized', null);
            }

//            try{
//                $id = $request->id;
//                $user = UserDetail::where('user_id',$request->user_id)->first();
//                $user->saved_opportunities()->detach($id);
//                $user->saved_opportunities()->attach($id);
//            }
//            catch(Exception $e){
//                return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
//            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
        return $this->apiResponse->sendResponse(200, 'Opportunity saved', null);
    }

    public function unsave_opportunity(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:opportunities',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                try {
                    $opp_id = $request->id;
                    $user = User::where('id', $user->id)->first();
                    $check = DB::table('opportunity_user')->select('opportunity_id')->where('user_id', $user->id)->get();
                    if ($check == null) {
                        return $this->apiResponse->sendResponse(200, 'Opportunity Unsaved', null);
                    } else {
                        foreach ($check as $c) {
                            if ($c->opportunity_id == $opp_id) {
                                DB::table('opportunity_user')->where([['user_id', $user->id], ['opportunity_id', $opp_id]])->delete();
                                return $this->apiResponse->sendResponse(200, 'Opportunity Unsaved', null);
                                break;

                            }
                        }
                    }

                } catch (Exception $e) {
                    return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
                }

            } else {
                $this->apiResponse->sendResponse(400, 'Not Authorized', null);
            }

//            $id = $request->id;
//            $user = UserDetail::where('user_id',$request->user_id)->first();
//            $user->saved_opportunities()->detach($id);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
        return $this->apiResponse->sendResponse(200, 'Opportunity Unsaved', null);
    }

    public function show_saved_opportunity()
    {
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            $opp_ids = DB::table('opportunity_user')->select('opportunity_id')->where('user_id', $user->id)->get();
            $opp_slug = [];
            foreach ($opp_ids as $opp_id) {
                $opp_slug[] = array('title' => DB::table('opportunity_translations')->select('title')->where([['opportunity_id', $opp_id->opportunity_id], ['locale', 'en']])->first(), 'desc' => DB::table('opportunities')->select('*')->where('id', $opp_id->opportunity_id)->get());
            }
            return $this->apiResponse->sendResponse(200, 'Success', $opp_slug);
        } else {
            return $this->apiResponse->sendResponse(500, 'Unauthorized', null);
        }
    }

    public function save_user_language(Request $request)
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

            if (!$pcheck) {
                $record = new UserDetail;
                $record->user_id = $user->id;
                $record->language_id = $request->id;
                $record->save();
                return $this->apiResponse->sendResponse(200, 'Success', null);
            } else {
                $pcheck->language_id = $request->id;
                $pcheck->save();
                return $this->apiResponse->sendResponse(200, 'Success', null);
            }
        } else {
            return $this->apiResponse->sendResponse(500, 'Users not logged in', null);
        }
    }

    public function save_user_filters(Request $request)
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
            } else {
                return $this->apiResponse->sendResponse(500, 'Un Authorized', null);
            }

            $user = User::where('id', $user->id)->first();
            $tags = $request->tags;
//            return $this->apiResponse->sendResponse(200, 'Saved filters selected by user', $tags);
            if (empty($tags)) {
                return $this->apiResponse->sendResponse(400, 'Select at least one filter', null);
            }

            // Read $tags as json
            $user->tags()->sync(json_decode($tags));
            return $this->apiResponse->sendResponse(200, 'Saved filters selected by user', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function get_all_countries(Request $request)
    {
        $countries = Country::all();
        return $this->apiResponse->sendResponse(200, 'All countries fetched.', $countries);
    }

    public function get_location($location_id)
    {
        try {
            $country = DB::table('opportunity_locations')->select('location')->where('id', $location_id)->get();
            return $this->apiResponse->sendResponse(200, 'Success', $country);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function get_funding_status($id)
    {
        try {
            $fund_status = DB::table('fund_types')->select('type')->where('id', $id)->get();
            return $this->apiResponse->sendResponse(200, 'Success', $fund_status);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function get_user_language()
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

    public function get_user_filters()
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

    public function segment_analytics(Request $request)
    {
        try {
//            Create new Analytics, connect it to user, connect it to opportunity
            $user = User::find($request->userId);
            $is_view_action = strcmp($request->event, "Views");

            $opportunity = null;
            if (array_key_exists("opp_id", $request->properties)) {
                $opportunity = Opportunity::find($request->properties["opp_id"]);
            }

            $action = Action::where('event', $request->event)->first();

            if ($is_view_action == 0) {
                if ($opportunity->views()->whereDate('created_at', '=', Carbon::today()->toDateString())->exists())
                    $opportunity->views()->whereDate('created_at', '=', Carbon::today()->toDateString())->increment('views');
                else
                    $opportunity->views()->create([1]);
                $opportunity->save();

                if ($request->properties["duration"] > 5000) {
                    $analytics = new Analytics;
                    $analytics->key = "duration";
                    $analytics->value = $request->properties["duration"];
                    $analytics->action()->associate($action);
                    $analytics->user()->associate($user);
                    $analytics->opportunity()->associate($opportunity);

                    $analytics->save();
                }
            } else {

                foreach ($request->properties as $key => $val) {
                    if (strcmp($key, "opp_id") == 0) {
                        if (count($request->properties) == 1) {
                            $analytics = new Analytics;
                            $analytics->action()->associate($action);
                            $analytics->user()->associate($user);
                            if (!is_null($opportunity))
                                $analytics->opportunity()->associate($opportunity);

                            $analytics->save();
                        }
                        continue;
                    }

                    $analytics = new Analytics;
                    $analytics->key = $key;
                    $analytics->value = $val;
                    $analytics->action()->associate($action);
                    $analytics->user()->associate($user);
                    if (!is_null($opportunity))
                        $analytics->opportunity()->associate($opportunity);

                    $analytics->save();
                }
            }
            return $this->apiResponse->sendResponse(200, 'Successfully added analytics', null);
        } catch (\Exception $e) {

            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTrace());
        }
    }
}
