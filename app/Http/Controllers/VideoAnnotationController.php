<?php

namespace App\Http\Controllers;

use App\AnnotationUserReport;
use App\Category;
use App\CategoryUserReport;
use App\Video;
use App\VideoAnnotation;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Config;

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
                'is_public' => 'sometimes|numeric|between:0,1'
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

                if (isset($request->is_public))
                    $is_public = $request->is_public;
                else
                    $is_public = 1;

                $videoAnnotation = new VideoAnnotation();
                $videoAnnotation->user_id = Auth::user()->id;
                $videoAnnotation->video_id = $video->id;
                $videoAnnotation->annotation = $request->annotation;
                $videoAnnotation->video_timestamp = $request->video_timestamp;
                $videoAnnotation->is_public = $is_public;
                $videoAnnotation->save();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video annotations added successfully', $videoAnnotation);
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
                return $this->apiResponse->sendResponse(404, 'No Video Found', null);
            } else {
                if (Auth::check()) {
                    $video_annotation = VideoAnnotation::with('user')->where(['video_id' => $video->id, 'is_public' => 1])->orWhere([['video_id', $video->id], ['is_public', 0], ['user_id', Auth::id()]])->get();
                } else {
                    $video_annotation = VideoAnnotation::with('user')->where(['video_id' => $video->id, 'is_public' => 1])->get();
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video Annotations Found successfully', $video_annotation);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function edit_video_note(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'note_id' => 'required|integer',
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $note = VideoAnnotation::find($request->note_id);
            if (!is_null($note)) {
                if ($note->user_id == Auth::user()->id) {
                    $note->annotation = $request->note;
                    $note->save();

                    $note = VideoAnnotation::with('user')->find($request->note_id);
                    DB::Commit();
                    return $this->apiResponse->sendResponse(200, 'Note updated successfully', $note);
                } else {
                    return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
                }
            }
            return $this->apiResponse->sendResponse(404, 'Note not found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function delete_video_note(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'note_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $note = VideoAnnotation::find($request->note_id);
            if (!is_null($note)) {
                if ($note->user_id == Auth::user()->id) {
                    $note->delete();
                    DB::Commit();
                    return $this->apiResponse->sendResponse(200, 'Note deleted successfully', null);
                } else {
                    return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
                }
            }
            return $this->apiResponse->sendResponse(404, 'Note not found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function change_note_privacy(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'is_public' => 'required|numeric|between:0,1',
            'note_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $change_privacy = VideoAnnotation::find($request->note_id)->update(['is_public' => $request->is_public]);
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Note privacy changed successfully', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_video_annotation_report(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_annotation_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (!VideoAnnotation::find($request->video_annotation_id)) {
                return $this->apiResponse->sendResponse(404, 'Video Annotation Not Found', null);
            }
 
            if (!AnnotationUserReport::where(['video_annotation_id' => $request->video_annotation_id, 'user_id' => Auth::user()->id])->first()) {
                Auth::user()->videoAnnotationReports()->attach($request->video_annotation_id);
            } else {
                return $this->apiResponse->sendResponse(200, 'Already Reported For this Video Annotation', null);
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Report For Video Annotation Added Succcessfully', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_category_report(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (!Category::find($request->category_id)) {
                return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
            }
 
            if (!CategoryUserReport::where(['category_id' => $request->category_id, 'user_id' => Auth::user()->id])->first()) {
                Auth::user()->categoryReports()->attach($request->category_id);
            } else {
                return $this->apiResponse->sendResponse(200, 'Already Reported For this Category', null);
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Report For Category Added Succcessfully', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
