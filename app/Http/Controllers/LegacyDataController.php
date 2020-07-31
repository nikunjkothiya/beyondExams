<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use App\PremiumValidity;
use App\User;
use App\UserDetail;
use Carbon\Carbon;
use Exception;
use DB;
use Log;

class LegacyDataController extends Controller
{

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function update_legacy_users()
    {
        try {
            $legacyDB = DB::connection('mysql_legacy');
            $users = DB::table('legacy_users')->get();
            $operated_profile = array();
            $operated_subs = array();
            $operated_tags = array();
            foreach ($users as $user) {

                // User Profile / Details
                $phoenixProfile = UserDetail::where('user_id', $user->phoenix_user_id)->first();
                $legacyProfile = $legacyDB->table('user_profile')->where('user_id', $user->legacy_user_id)->first();
                if (is_null($phoenixProfile) && !is_null($legacyProfile)) {
                    $fill_details = new UserDetail();
                    $fill_details->user_id = $user->phoenix_user_id;
                    $fill_details->email = $legacyProfile->email;
                    $fill_details->firstname = $legacyProfile->first_name;
                    $fill_details->lastname = $legacyProfile->last_name;
                    $fill_details->college = $legacyProfile->college;
                    $fill_details->city = $legacyProfile->city;

                    $gpaType = gettype($legacyProfile->gpa);
                    if($gpaType == 'integer' || $gpaType == 'double' || $gpaType == 'float'){
                        if($legacyProfile->gpa > 0 && $legacyProfile->gpa < 10){
                            $fill_details->gpa = $legacyProfile->gpa;
                        }
                        if($legacyProfile->gpa > 10 && $legacyProfile->gpa < 100){
                            $fill_details->gpa = ($legacyProfile->gpa)/10;
                        }
                    }


                    $fill_details->save();
                    array_push($operated_profile, $user->phoenix_user_id);
                }

                // Deadlines / Premium Subscription
                $phoenixSubscription = PremiumValidity::where('user_id', $user->phoenix_user_id)->first();
                $legacyDeadline = $legacyDB->table('deadlines')->where('user_id', $user->legacy_user_id)->first();
                if (is_null($phoenixSubscription) && !is_null($legacyDeadline)) {
                    $fill_deadline = new PremiumValidity();
                    $fill_deadline->user_id = $user->phoenix_user_id;
                    $fill_deadline->end_date = Carbon::parse($legacyDeadline->end_date);
                    $fill_deadline->save();
                    array_push($operated_subs, $user->phoenix_user_id);
                }

                // Filters / Tag
                $legacyFilter = $legacyDB->table('filters')->where('user_id', $user->legacy_user_id)->first();
                $phoenixTags = DB::table('tag_user')->select('tag_id')->where('user_id', $user->phoenix_user_id)->get();
                if (!is_null($legacyFilter) && count($phoenixTags) == 0) {
                    // Insert Tags in phoenix
                    if($legacyFilter->admission == 1){
                        DB::table('tag_user')->insert(['user_id' => $user->phoenix_user_id, 'tag_id' => 6]);
                    }
                    if($legacyFilter->awards == 1){
                        DB::table('tag_user')->insert(['user_id' => $user->phoenix_user_id, 'tag_id' => 4]);
                    }
                    if($legacyFilter->competitons == 1){
                        DB::table('tag_user')->insert(['user_id' => $user->phoenix_user_id, 'tag_id' => 2]);
                    }
                    if($legacyFilter->conferences == 1){
                        DB::table('tag_user')->insert(['user_id' => $user->phoenix_user_id, 'tag_id' => 1]);
                    }
                    if($legacyFilter->fellowships == 1){
                        DB::table('tag_user')->insert(['user_id' => $user->phoenix_user_id, 'tag_id' => 8]);
                    }
                    if($legacyFilter->grants == 1){
                        DB::table('tag_user')->insert(['user_id' => $user->phoenix_user_id, 'tag_id' => 9]);
                    }
                    if($legacyFilter->internships == 1){
                        DB::table('tag_user')->insert(['user_id' => $user->phoenix_user_id, 'tag_id' => 5]);
                    }
                    if($legacyFilter->scholarships == 1){
                        DB::table('tag_user')->insert(['user_id' => $user->phoenix_user_id, 'tag_id' => 3]);
                    }
                    if($legacyFilter->summer_winter_schools == 1){
                        DB::table('tag_user')->insert(['user_id' => $user->phoenix_user_id, 'tag_id' => 7]);
                    }
                    array_push($operated_tags, $user->phoenix_user_id);
                }

                $resp['profile'] = $operated_profile;
                $resp['subs'] = $operated_subs;
                $resp['tags'] = $operated_tags;
            }
            return $this->apiResponse->sendResponse(200, 'Operation Successful', $resp);
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
            foreach ($request->subscriptions as $subscription) {
                $user = User::where('email', $subscription['email_id'])->first();
                if (is_null($user)) {
                    array_push($invalidUsers, $subscription);
                } else {
                    $insertPlan = new PremiumValidity();
                    $insertPlan->user_id = $user['id'];
                    $insertPlan->end_date = Carbon::parse($subscription['end_date']);
                    $insertPlan->save();
                }
            }
            return $this->apiResponse->sendResponse(200, 'Operation Successful', $invalidUsers);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
