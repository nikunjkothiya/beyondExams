<?php

namespace App\Http\Controllers;

use App\Country;
use App\DeveloperDetail;
use App\Discipline;
use App\Language;
use App\Qualification;
use App\Tag;
use App\UserDetail;
use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @property ApiResponse apiResponse
 */
class PreciselyController extends Controller
{
    public function __construct(ApiResponse $apiResponse)
    {
        $this->msg = "";
        $this->apiResponse = $apiResponse;
    }

    public function get_language(Request $request)
    {
        try {
            $languages = DB::table('languages')->select('code', 'language', 'language_example')->get();

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

            $user_id = $request->user()->id;

            $check = UserDetail::where('user_id', $user_id)->first();

            if (is_null($check)) {
                $record = new UserDetail;
                $record->user_id = $user_id;
                $record->language_id = $request->language_code;
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
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error 3.', $e->getMessage());
        }
    }

//    TODO: CORRECT RETURN TYPE
    public function get_profile(Request $request){
        try{
            $pcheck = UserDetail::where('user_id',$request->user()->id)->first();
        }
        catch(Exception $e){
            return $this->apiResponse->sendResponse(500, 'User authentication failed', $e->getMessage());
        }

        if($pcheck){
            $countries = Country::all();
            $disciplines = Discipline::all();
            $qualifications = Qualification::all();
            return view('pages.profile',['languages'=>$this->languages,'pcheck'=>$pcheck,'countries'=>$countries,'disciplines'=>$disciplines,'qualifications'=>$qualifications,'txnflag'=>$this->txnflag->check_subscription(Auth::user()->id)]);
        }
        return redirect('/setup/2');

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

            $id = $request->id;
            $user = $request->user();
            $user->saved_opportunities()->detach($id);
            $user->saved_opportunities()->attach($id);
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
            $user = $request->user();
            $user->saved_opportunities()->detach($id);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }

        return $this->apiResponse->sendResponse(200, 'Opportunity removed from saved', null);
    }

    public function save_user_language(Request $request) {
        try {

            $validator = Validator::make($request->all(), [
                'language_id' => 'required|exists:languages',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }


            $user = $request->user();
            $check = UserDetail::where('user_id', $user->id)->first();
            $check->language_id = $request->language_id;


        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }

    public function save_user_filter(Request $request){
        try{
            $user = $request->user();
            $tags = $request->tags;
            if(empty($tags)){
                return $this->apiResponse->sendResponse(400, 'Select at least one filter', null);
            }
            $user->tags()->sync($tags);
            return $this->apiResponse->sendResponse(200, 'Saved filters selected by user', null);
        }
        catch(Exception $e){
            return $this->apiResponse->sendResponse(500, 'Internal server error.', $e->getMessage());
        }
    }
}
