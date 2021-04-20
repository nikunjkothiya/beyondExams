<?php

namespace App\Http\Controllers;

use App\AnnotationUserReport;
use App\Category;
use App\CategoryUserReport;
use App\Video;
use App\VideoAnnotation;
use App\VideoNoteTotalVote;
use App\VideoNoteVote;
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
                return $this->apiResponse->sendResponse(409, 'Already Reported For this Video Annotation', null);
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
                return $this->apiResponse->sendResponse(409, 'Already Reported For this Category', null);
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Report For Category Added Succcessfully', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_video_note_vote(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'note_id' => 'required|integer',
            'vote' => 'required|integer|min:-1|max:1',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $note = VideoAnnotation::find($request->note_id);
            if ($note) {
                $findVote = VideoNoteVote::where(['user_id' => Auth::user()->id, 'note_id' => $request->note_id])->first();
                if ($findVote) {
                    $old_vote = VideoNoteVote::where(['user_id' => Auth::user()->id, 'note_id' => $request->note_id])->first();
                    if ($request->vote == -1) {
                        if ($request->vote == $old_vote->vote) {
                            $message = 'Already Given Downvote';
                            $data = $findVote;
                            $statusCode = 409;
                        } else {
                            $findVote->vote = $request->vote;
                            $findVote->save();

                            if ($old_vote->vote == 1) {
                                $message = 'Vote Change From Upvote to Downvote Successfully';
                            } else {
                                $message = 'Vote Change From No-vote to Downvote Successfully';
                            }

                            $this->video_note_vote_count($request->note_id, $request->vote, $old_vote->vote);
                            $data = $findVote;
                            $statusCode = 200;
                        }
                    } else if ($request->vote == +1) {
                        if ($request->vote == $old_vote->vote) {
                            $message = 'Already Given Upvote';
                            $data = $findVote;
                            $statusCode = 409;
                        } else {
                            $findVote->vote = $request->vote;
                            $findVote->save();

                            if ($old_vote->vote == -1) {
                                $message = 'Vote Change From Downvote to Upvote Successfully';
                            } else {
                                $message = 'Vote Change From No-vote to Upvote Successfully';
                            }

                            $this->video_note_vote_count($request->note_id, $request->vote, $old_vote->vote);
                            $data = $findVote;
                            $statusCode = 200;
                        }
                    } else if ($request->vote == 0) {
                        if ($request->vote == $old_vote->vote) {
                            $message = 'Already Given No-vote';
                            $data = $findVote;
                            $statusCode = 409;
                        } else {
                            $findVote->vote = $request->vote;
                            $findVote->save();

                            if ($old_vote->vote == -1) {
                                $message = 'Vote Change From Downvote to No-vote Successfully';
                            } else {
                                $message = 'Vote Change From Upvote to No-vote Successfully';
                            }

                            $this->video_note_vote_count($request->note_id, $request->vote, $old_vote->vote);
                            $data = $findVote;
                            $statusCode = 200;
                        }
                    }

                    DB::Commit();
                    return $this->apiResponse->sendResponse($statusCode, $message, $data);
                } else {

                    $newVote = new VideoNoteVote();
                    $newVote->user_id = Auth::user()->id;
                    $newVote->note_id = $request->note_id;
                    $newVote->vote = $request->vote;
                    $newVote->save();

                    $this->video_note_vote_count($request->note_id, $request->vote, null);

                    DB::Commit();
                    return $this->apiResponse->sendResponse(200, 'Vote added successfully', $newVote);
                }
            }
            DB::Commit();
            return $this->apiResponse->sendResponse(404, 'Note / Annotation not found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_video_note_total_votes(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'note_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $note = VideoAnnotation::find($request->note_id);
            if (!$note) {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Note / Annotation Not Found', null);
            } else {
                $find = VideoNoteTotalVote::where('video_annotation_id', $request->note_id)->select('total_upvote','total_downvote')->first();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video Annotations Total Votes get Successfully', $find);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    private function video_note_vote_count($note_id, $vote, $old_vote)
    {
        $find = VideoNoteTotalVote::where('video_annotation_id', $note_id)->first();
        if ($find) {
            if ($vote == -1) {
                $find->total_downvote += 1;
                if ($old_vote == 1) {
                    $find->total_upvote -= 1;
                }
            } else if ($vote == 1) {
                $find->total_upvote += 1;
                if ($old_vote == -1) {
                    $find->total_downvote -= 1;
                }
            } else {
                if ($old_vote == -1) {
                    $find->total_downvote -= 1;
                }
                if ($old_vote == 1) {
                    $find->total_upvote -= 1;
                }
            }
            $find->save();
        } else {
            $newCreate = new VideoNoteTotalVote();
            $newCreate->video_annotation_id = $note_id;
            if ($vote == 1) {
                $newCreate->total_upvote = 1;
            } else if ($vote == -1) {
                $newCreate->total_downvote = 1;
            }
            $newCreate->save();
        }
        return true;
    }
}
