<?php

namespace App\Http\Controllers;

use App\Note;
use App\Test;
use App\Video;
use App\TestScore;
use App\VideoNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class LWYResourceController extends Controller
{
    //
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }


    function upload_notes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'pdf_file' => 'required|file|mimes:pdf',
            'resource_url' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }


        $file = $request->file('pdf_file');

        $ext = "." . pathinfo($_FILES["pdf_file"]["name"])['extension'];


        $name = time() . uniqid() . $ext;


        $contents = file_get_contents($file);

        $filePath = "lwy_notes/" . $name;

        Storage::disk('s3')->put($filePath, $contents);

        $title = "video-notes-" . substr(hash('sha256', mt_rand() . microtime()), 0, 3);

        if ($request->title)
            $title = $request->title;

        $note = Note::create(['title' => $title, 'url' => $filePath, 'resource_url' => $request->resource_url]);

        $note->save();


        return $this->apiResponse->sendResponse(200, 'Note added successfully', $note);
    }

    function get_notes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resource_url' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        return $this->apiResponse->sendResponse(200, 'Notes fetched successfully', Note::where('resource_url', $request->resource_url)->get());
    }

    function upload_test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'mcqs' => 'required|json',
            'video_id' => 'required|string',
            // 'resource_url' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $title = "video-test-" . substr(hash('sha256', mt_rand() . microtime()), 0, 3);

        if ($request->title)
            $title = $request->title;

        $video = Video::where('url', $request->video_id)->first();

        if (!$video) {
            $video = new Video(['url' => $request->video_id]);
            $video->save();
        }

        // $test = Test::create(['title'=>$title, 'mcqs'=>$request->mcqs, 'resource_url'=>$request->resource_url]);
        $test = Test::create(['title' => $title, 'mcqs' => $request->mcqs, 'resource_url' => $request->video_id]);

        $test->save();

        return $this->apiResponse->sendResponse(200, 'Test added successfully', $test);
    }

    public function get_tests(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $video = Video::where('url', $request->video_id)->first();

        if (!$video) {
            $video = new Video(['url' => $request->video_id]);
            $video->save();
        }

        return $this->apiResponse->sendResponse(200, 'Tests fetched successfully', Test::where('resource_url', $video->id)->get());
    }

    public function get_test_scores(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user = Auth::user();
        if ($user->role()->value('is_mentor') != 1) {
            return $this->apiResponse->sendResponse(404, 'User is not authorized', null);
        } else {
            $test = Test::find($request->test_id);
            return $this->apiResponse->sendResponse(200, 'Test scores fetched successfully', $test->scores()->with('user')->get());
        }
    }

    public function submit_test_score(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'score' => 'required|integer',
            'test_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user = Auth::user();
        $test = Test::find($request->test_id);

        $test_score = new TestScore();
        $test_score->score = $request->score;
        $test_score->user_id = $user->id;

        $test->scores()->save($test_score);
        $test_score->save();

        return $this->apiResponse->sendResponse(200, 'Test score added successfully', null);
    }
}
