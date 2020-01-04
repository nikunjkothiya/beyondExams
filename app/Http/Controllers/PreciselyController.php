<?php

namespace App\Http\Controllers;

use App\Country;
use App\Discipline;
use App\Language;
use App\Qualification;
use App\Tag;
use App\UserDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use App\User;
use App\DeveloperDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @property \App\Http\Controllers\ApiResponse apiResponse
 */
class PreciselyController extends Controller
{
    public function __construct(ApiResponse $apiResponse){
        $this->msg="";
        $this->apiResponse=$apiResponse;
    }

    public function get_language(Request $request) {
        try{
            $languages = DB::table('languages')->select('code', 'language','language_example')->get();

            return $this->apiResponse->sendResponse(200,'All languages fetched successfully', $languages);
        }
        catch(Exception $e){
            return $this->apiResponse->sendResponse(500,'Internal Server Error', $e);
        }
    }


    public function get_filters(Request $request) {
        try{
            $filters = Tag::all();

            return $this->apiResponse->sendResponse(200,'All languages fetched successfully', $filters);
        }
        catch(Exception $e){
            return $this->apiResponse->sendResponse(500,'Internal Server Error', $e);
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

            if($validator->fails()){
                return $this->apiResponse->sendResponse(400,'Parameters missing or invalid.',$validator->errors());
            }

            $check = UserDetail::where('user_id', $request->id)->first();

            if (is_null($check)) {
                $record = new UserDetail;
                $record->user_id = $request->id;
                $record->language_id = Language::where('code', \Config::get('app.locale'))->first()->id;
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
                    return $this->apiResponse->sendResponse(200,'User details saved.', $record);
                } else {
                    return $this->apiResponse->sendResponse(500,'Internal server error. New record could not be inserted', null);
                }
            } else {
                $check->language_id = Language::where('code', \Config::get('app.locale'))->first()->id;
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
                    return $this->apiResponse->sendResponse(200,'User details saved.', $check);
                } else {
                    return $this->apiResponse->sendResponse(500,'Internal server error. Record could not be updated', null);
                }
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500,'Internal server error 3.', $e->getMessage());
        }
    }
}
