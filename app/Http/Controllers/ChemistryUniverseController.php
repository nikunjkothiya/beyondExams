<?php

namespace App\Http\Controllers;

use App\Feedback;
use App\LearningPath;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChemistryUniverseController extends Controller
{
    //
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function get_feedbacks()
    {
        return $this->apiResponse->sendResponse(200, 'Feedbacks fetched successfully.', Feedback::get());
    }

    public function save_feedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string',
            'avatar' => 'string',
            'message' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        Feedback::create(['name'=>$request->name, 'email'=>$request->email, 'avatar'=>$request->avatar, 'message'=>$request->avatar]);

        return $this->apiResponse->sendResponse(200, 'Feedback saved successfully.', null);
    }
}
