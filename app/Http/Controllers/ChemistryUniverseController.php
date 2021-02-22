<?php

namespace App\Http\Controllers;

use App\Feedback;
use App\Ses;
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

        Feedback::create(['name' => $request->name, 'email' => $request->email, 'avatar' => $request->avatar, 'message' => $request->avatar]);

        return $this->apiResponse->sendResponse(200, 'Feedback saved successfully.', null);
    }

    public function add_ses_video(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_url' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        $video = Video::where('url', $request->video_url)->first();
        if (!$video)
            $video = new Video(['url' => $request->video_url]);

        $video->save();

        $ses = new Ses();
        $ses->video_id = $video->id;
        $ses->save();

        return $this->apiResponse->sendResponse(200, 'SES video saved successfully.', null);
    }

    public function get_ses_videos(){
        return $this->apiResponse->sendResponse(200, 'SES video saved successfully.', Ses::with('video')->paginate(15));
    }
}
