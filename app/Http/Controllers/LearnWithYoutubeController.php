<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Country;
use App\Language;
use App\LearningPath;
use App\Role;
use App\UserHistory;
use App\Video;
use Auth;
use Config;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LearnWithYoutubeController extends Controller
{
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    //
    public function submit_feedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string',
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


        DB::table('feedbacks')->insert(['name' => $name, 'email' => $email, 'message' => $request->message]);

        return $this->apiResponse->sendResponse(200, 'Feedback saved successfully', null);

    }

    public function submit_user_profile(Request $request)
    {
        try {
            if (Auth::check()) {

                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email',
                    'college' => 'required|string|max:1024',
                    'age' => 'required|int',
                    'country' => 'required|integer|min:1|max:' . Country::count(),
                    'profile_link' => 'string',
                    'phone' => 'integer',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                $user = Auth::user();

                // Save data to users table
                $user->name = $request->name;
                $user->email = $request->email;

                if (isset($request->profile_link))
                    $user->profile_link = $request->profile_link;

                $user->language_id = Language::where('code', Config::get('app.locale'))->first()->id;
                $slug = str_replace(" ", "-", strtolower($request->name)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 3);
                $user->slug = $slug;
                $user->age = $request->age;
                $user->country_id = $request->country;
                $user->phone = $request->phone;

                $user->save();

                return $this->apiResponse->sendResponse(200, 'User details saved', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    //    TODO: CORRECT RETURN TYPE
    public function get_user_profile()
    {
        if (Auth::check()) {
            $user = Auth::user();

            return $this->apiResponse->sendResponse(200, 'Successfully fetched user profile.', $user);
        } else {
            return $this->apiResponse->sendResponse(500, 'User profile not complete', null);
        }
    }

    public function addNewCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'level' => 'required|integer',
            'parent_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        if (Auth::user()->role() == Role::find(1))
            return $this->apiResponse->sendResponse(401, 'User unauthorised.', null);

        $category = Category::create(['title' => $request->title, 'level' => $request->level, 'parent_id' => $request->parent_id]);

        return $this->apiResponse->sendResponse(200, 'New Category added', $category);
    }

    public function getNextLevel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|integer',
            'parent_id' => 'integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        if ($request->level > 1) {
            if (!$request->parent_id) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing.', 'parent_id parameter not included');
            }
            $parent_id = $request->parent_id;
        } else {
            $parent_id = 0;
        }

        $categories = Category::where('level', $request->level)->where('parent_id', $parent_id)->get();
        if (count($categories) == 0) {
            $request->request->add(['category_id' => $parent_id]);
            return $this->get_learning_path($request);
        }

        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories);
    }

    public function get_learning_path(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        return $this->apiResponse->sendResponse(200, 'Learning path fetched successfully', LearningPath::with('video')->where('category_id', $request->category_id)->orderBy('ordering', 'asc')->get());
    }

    public function getAllCategories(Request $request)
    {
        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories = Category::get());
    }

    public function getCategories(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'required|integer',
            'parent_id' => 'integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        if ($request->level > 1) {
            if (!$request->parent_id) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing.', 'parent_id parameter not included');
            }
            $parent_id = $request->parent_id;
        } else {
            $parent_id = 0;
        }

        $categories = Category::where('level', $request->level)->where('parent_id', $parent_id)->get();

        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories);

    }

    public function removeCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        Category::find($request->category_id)->delete();

        return $this->apiResponse->sendResponse(200, 'Category deleted successfully', null);
    }

    public function get_resource_comments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Need a resource Id', $validator->errors());
        }

        $video = Video::where('url', $request->resource_id)->first();

        if (!$video) {
            $video = new Video(['url' => $request->resource_id]);
            $video->save();
        }

        // Send notification via Notification controller function or guzzle
        return $this->apiResponse->sendResponse(200, 'Success', Comment::where('video_id', $video->id)->paginate(10));
    }

    public function get_resource_likes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Need a resource Id', $validator->errors());
        }

        $num_likes = Video::where('url', $request->video_url)->first()->num_likes();
        // Send notification via Notification controller function or guzzle
        return $this->apiResponse->sendResponse(200, 'Success', $num_likes);
    }

    public function add_resource_comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $video = Video::where('url', $request->video_url)->first();

        if (!$video) {
            $video = new Video(['url' => $request->video_url]);
            $video->save();
        }

        $comment = new Comment();
        $comment->message = $request->message;
        $comment->user_id = Auth::id();
        $comment->video_id = $video->id;
        $comment->save();

        // Send notification via Notification controller function or guzzle
        return $this->apiResponse->sendResponse(200, 'Comment added successfully', $comment);
    }

    public function switch_video_like(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $video = Video::where('url', $request->video_url)->first();
        if (!$video) {
            $video = new Video(['url' => $request->video_url]);
            $video->save();
        }

        Auth::user()->videos()->toggle([array('video_id' => $video->id, 'type' => 'liked')]);

        return $this->apiResponse->sendResponse(200, 'Like Updated successfully', null);
    }

    public function addToWatchHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
            'start_time' => 'date|date_format:H:i:s',
            'end_time' => 'date|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $video = Video::where('url', $request->video_url)->first();
        if (!$video)
            $video = new Video(['url' => $request->video_url]);

        $video->save();

//        if (!Auth::user()->videos()->where('video_id', $video->id))
            Auth::user()->videos()->attach([['video_id' => $video->id, 'type' => 'history']]);

        if ($request->start_time && $request->end_time) {

        }

        return $this->apiResponse->sendResponse(200, 'Video saved to history', null);
    }

    public function getWatchHistory()
    {
        return $this->apiResponse->sendResponse(200, 'Video saved to history', Auth::user()->history()->orderBy('id', 'desc')->paginate(20));
    }

    public function addToSearchHistory()
    {

    }

    public function uniquelyIdentifyDevice(Request $request)
    {

    }

    public function add_video_to_learning_path(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|int',
            'video_url' => 'string',
            'ordering' => 'required|int'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        $video = Video::where('url', $request->video_url)->first();
        if (!$video)
            $video = new Video(['url' => $request->video_url]);

        $video->save();

        if ($request->ordering == -1) {
            $lp = LearningPath::where('category_id', $request->category_id)->orderBy('ordering', 'desc')->first();
            if ($lp)
                $ordering = $lp->ordering + 1;
            else
                $ordering = 1;
        } else {
            $ordering = $request->ordering;
        }


        $new_lp_id = LearningPath::create(['category_id' => $request->category_id, 'video_id' => $video->id, 'ordering' => $ordering]);

        return $this->apiResponse->sendResponse(200, 'Learning path updated', $new_lp_id);
    }
}
