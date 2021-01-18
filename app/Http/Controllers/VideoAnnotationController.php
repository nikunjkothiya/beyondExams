<?php

namespace App\Http\Controllers;

use App\Video;
use DB;
use App\VideoAnnotation;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DateTime;

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
      //  if (Auth::check()) {
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

        $video_id = $video->id;
        $videoAnnotation = new VideoAnnotation();
        $videoAnnotation->video_id = $video_id;
        $videoAnnotation->annotation = $request->annotation;
        $videoAnnotation->video_timestamp =  $request->video_timestamp;
        $videoAnnotation->save();

        DB::commit();
        return $this->apiResponse->sendResponse(200, 'Video annotations added successfully', null);
    } catch (\Exception $e) {
        DB::rollback();
        throw new HttpException(500, $e->getMessage());
    }
  //  } else {
  //      return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
  //  }

}

public function get_video_annotations(Request $request)
    {
        DB::beginTransaction();
      //  if (Auth::check()) {
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
        }else{
            $video_annotation = VideoAnnotation::select('annotation')->where('video_id', '=', $video->id)->get();
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Video Annotations Found successfully', $video_annotation);
        }

    } catch (\Exception $e) {
        DB::rollback();
        throw new HttpException(500, $e->getMessage());
    }
   // } else {
   //     return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
   // }

}
}
