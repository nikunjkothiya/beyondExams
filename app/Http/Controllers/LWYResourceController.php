<?php

namespace App\Http\Controllers;

use App\Note;
use App\Test;
use App\Video;
use App\TestScore;
use App\VideoNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class LWYResourceController extends Controller
{
    //
    private $apiResponse;
    private $aws_base_url = "https://precisely-test1221001-dev.s3.ap-south-1.amazonaws.com/";

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }


    public function upload_video_material(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'pdf_file' => 'required|file|mimes:pdf',
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        try {
            $find_video = Video::where('url', $request->video_url)->first();

            if (!$find_video) {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Video Not Found', null);
            }

            $file = $request->file('pdf_file');
            $bytes = filesize($file);
            $file_size = $this->formatSizeUnits($bytes);

            if ($request->title) {
                $title = $request->title;
            } else {
                $title = "video-notes-pdf" . substr(hash('sha256', mt_rand() . microtime()), 0, 5);
            }

            $pdftext = file_get_contents($file);
            $pages = preg_match_all("/\/Page\W/", $pdftext, $dummy);

            /*
            $storage_path = 'video_reading_material/';
            $pdf_filepath = commonUploadFile($storage_path, $file); */

            $ext = "." . pathinfo($_FILES["pdf_file"]["name"])['extension'];
            $name = time() . uniqid() . $ext;
            $contents = file_get_contents($file);
            $filePath = "lwy_notes/" . $name;
            Storage::disk('s3')->put($filePath, $contents);

            $note = new Note();
            $note->user_id = Auth::user()->id;
            $note->title = $title;
            $note->url = $this->aws_base_url . $filePath;
            $note->resource_url = $request->video_url;
            $note->size = $file_size;
            $note->total_pages = $pages;
            $note->save();

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Reading Material added successfully', $note);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_video_materials(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        try {
            $notes = Note::with('user:id,name,avatar')->where('resource_url', $request->video_url)->get();
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Reading Material fetched successfully', $notes);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function upload_test(Request $request)
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

    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
}
