<?php

namespace App\Http\Controllers;

use App\Country;
use App\DeveloperDetail;
use App\Discipline;
use App\Language;
use App\Qualification;
use App\Tag;
use App\User;
use App\UserDetail;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
        $this->txnflag = new SubscriptionController;
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

            return $this->apiResponse->sendResponse(200, 'All languages fetched successfully', $filters);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    public function submit_profile(Request $request) 
    {
        try {
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $user_id=$user->id;
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
                }}
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error 3.', $e->getMessage());
        }
    }

//    TODO: CORRECT RETURN TYPE
    public function get_profile(Request $request){
        if (Auth::check()) {
            $user = User::find(Auth::user()->id);
            try{
                $pcheck = UserDetail::where('user_id',$user->id)->first();
            }
            catch(Exception $e){
                return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
            }

            if($pcheck){
                $countries = Country::all();
                $disciplines = Discipline::all();
                $qualifications = Qualification::all();
                $data['user_details'] = $pcheck;
                $data['txnflag']=$this->txnflag->check_subscription($request->user_id);

                return $this->apiResponse->sendResponse(200, 'Successfully fetched user profile.', $data);
            }
        }
        else{
        return $this->apiResponse->sendResponse(500, 'Users not logged in', null);}         
            
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

            try{
                $id = $request->id;
                $user = UserDetail::where('user_id',$request->user_id)->first();
                $user->saved_opportunities()->detach($id);
                $user->saved_opportunities()->attach($id);
            }
            catch(Exception $e){
                return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
            }
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

            $id = $request->id;
            $user = UserDetail::where('user_id',$request->user_id)->first();
            $user->saved_opportunities()->detach($id);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }

        return $this->apiResponse->sendResponse(200, 'Opportunity removed from saved', null);
    }

    public function save_user_language(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:languages',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }


            //$user = $request->user();
            if (Auth::check()) {
                $user = User::find(Auth::user()->id);
                $user_id = $user->id;}
            else{return $this->apiResponse->sendResponse(500, 'UnAuthorized', null);}

            $check = UserDetail::where('user_id', $user_id)->first();
            //$check->user_id = $user_id;
            //$check->language_id = $request->language_id;
            
            if (is_null($check)) {
                $record = new UserDetail;

                $record->user_id = $user_id;
                $record->language_id = $request->language_id;
                $record->save();
                if ($record) {
                    return $this->apiResponse->sendResponse(200, 'User details saved.', $record);
                } else {
                    return $this->apiResponse->sendResponse(500, 'Internal server error. New record could not be inserted', null);
                }
            } else {
                $check->language_id = $request->language_id;
                $check->save();
                if ($check) {
                    return $this->apiResponse->sendResponse(200, 'User details saved.', $check);
                } else {
                    return $this->apiResponse->sendResponse(500, 'Internal server error. Record could not be updated', null);
                }
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
        
    }
 
    public function save_user_filters(Request $request){
        try{
            $user = User::where('id', $request->user_id)->first();
            $tags = $request->tags;
//            return $this->apiResponse->sendResponse(200, 'Saved filters selected by user', $tags);
            if(empty($tags)){
                return $this->apiResponse->sendResponse(400, 'Select at least one filter', null);
            }
            // dd(json_decode($tags));
            // Read $tags as json
            $user->tags()->sync(json_decode($tags));
            return $this->apiResponse->sendResponse(200, 'Saved filters selected by user', null);
        }
        catch(Exception $e){
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function get_all_countries(Request $request){
        $countries = Country::all();
        return $this->apiResponse->sendResponse(200, 'All countries fetched.', $countries);
    }

    public function get_location($location_id){
        try{
        $country = DB::table('opportunity_locations')->select('location')->where('id',$location_id)->get();
        return $this->apiResponse->sendResponse(200, 'Success', $country);
        }
        catch(Exception $e){
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function get_funding_status($id){
        try{
        $fund_status = DB::table('fund_types')->select('type')->where('id',$id)->get();
        return $this->apiResponse->sendResponse(200, 'Success', $fund_status);
        }
        catch(Exception $e){
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }
}
