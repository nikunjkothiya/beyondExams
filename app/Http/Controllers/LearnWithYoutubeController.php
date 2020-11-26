<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class LearnWithYoutubeController extends Controller
{
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }
    //
    public function submit_feedback(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'integer',
            'message' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        $name = '';
        if ($request->name)
            $name = $request->name;

        $email = '';
        if ($request->email)
            $email = $request->email;


        DB::table('feedbacks')->insert(['name'=>$name, 'email' => $email, 'message'=>$request->message]);

        return $this->apiResponse->sendResponse(200, 'Feedback saved successfully', null);

    }
}
