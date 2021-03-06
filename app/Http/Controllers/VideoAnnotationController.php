<?php

namespace App\Http\Controllers;

use App\Video;
use App\VideoAnnotation;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VideoAnnotationController extends Controller
{
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function add_video_annotations(Request $request)
    {
        DB::beginTransaction();
        if (Auth::check()) {
            $validator = Validator::make($request->all(), [
                'video_url' => 'required|string',
                'annotation' => 'required|string',
                'video_timestamp' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            try {
                $video = Video::where('url', $request->video_url)->first();

                if (!$video) {
                    $video = new Video(['url' => $request->video_url]);
                    $video->save();
                }

                $videoAnnotation = new VideoAnnotation();
                $videoAnnotation->video_id = $video->id;
                $videoAnnotation->annotation = $request->annotation;
                $videoAnnotation->video_timestamp = $request->video_timestamp;
                $videoAnnotation->save();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video annotations added successfully', null);
            } catch (\Exception $e) {
                DB::rollback();
                return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
            }
        } else {
            return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
        }
  }

    public function get_video_annotations(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $video = Video::where('url', $request->video_url)->first();

            if (!$video) {
                return $this->apiResponse->sendResponse(200, 'No Video Found', null);
            } else {
                $video_annotation = VideoAnnotation::where('video_id', '=', $video->id)->get();
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video Annotations Found successfully', $video_annotation);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
