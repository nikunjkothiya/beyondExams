<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Country;
use App\Language;
use App\LearningPath;
use App\UserHistory;
use App\User;
use App\Video;
use App\BookmarkVideo;
use App\HistoryUserVidoes;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Search;
use App\VideoRating;
use App\AttemptTest;
use App\CategoryUserEnrollment;
use App\CategoryUserRating;
use App\CategoryUserReport;
use App\Keyword;
use App\KeywordUser;
use App\KeywordVideo;
use App\VideoAnnotation;
use File;
use Illuminate\Support\Facades\Config;

class CourseController extends Controller
{
    private $apiResponse;
    // private $aws_base_url = "https://precisely-test1221001-dev.s3.ap-south-1.amazonaws.com";

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function addNewCategory(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'level' => 'required|integer',
            'parent_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->role->contains(2)) {
                $find = Category::where([['title', strtolower($request->title)], ['parent_id', $request->parent_id]])->first();
                if (!$find) {
                    $description = ($request->description) ? $request->description : null;

                    $slug = str_replace(" ", "-", strtolower($request->title)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 5);

                    if ($request->image) {
                        $cate_image = $request->file('image');
                        $storage_path = 'category/images/';
                        $imgpath = commonUploadFile($storage_path, $cate_image);
                        $full_imgpath = env('BASE_URL') . $imgpath;
                    }

                    $image = ($request->image) ? $full_imgpath : null;
                    $category = Category::create(['user_id' => Auth::user()->id, 'title' => $request->title, 'description' => $description, 'image_url' => $image, 'level' => $request->level, 'parent_id' => $request->parent_id, 'slug' => $slug]);
                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'New Category added', $category);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(409, 'Already Category have', $find);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Only Teacher can add category', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function edit_category(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'title' => 'string',
            'description' => 'string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->role->contains(2)) {

                $findCategory = Category::where(['id' => $request->category_id, 'user_id' => Auth::user()->id])->first();
                if ($findCategory) {
                    if ($request->title) {
                        $findCategory->title = $request->title;
                        $slug = str_replace(" ", "-", strtolower($request->title)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 5);
                        $findCategory->slug = $slug;
                    }
                    if ($request->description) {
                        $findCategory->description = $request->description;
                    }

                    if ($request->image) {
                        $cate_image = $request->file('image');
                        $storage_path = 'category/images/';
                        $imgpath = commonUploadFile($storage_path, $cate_image);
                        $full_imgpath = env('BASE_URL') . $imgpath;
                        $image = ($request->image) ? $full_imgpath : $findCategory->image_url;
                        $findCategory->image_url = $image;
                    }

                    $findCategory->save();

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Category Update Successfully', $findCategory);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Category not found', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Only Teacher can add category', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_category_rating(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'rating' => 'required|integer|between:1,5',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $find_category = Category::find($request->category_id);
            if ($find_category) {
                $already = CategoryUserRating::where(['user_id' => Auth::user()->id, 'category_id' => $request->category_id])->first();
                if (!$already) {
                    $find_category->rating_sum += $request->rating;
                    $find_category->rated_user += 1;
                    $find_category->save();

                    Auth::user()->categoryRating()->attach($find_category->id, array('rating' => $request->rating));
                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Category Rating Added Successfully', $find_category);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(409, 'Already Added Rating', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_category_rating(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $find_category = Category::find($request->category_id);
            DB::commit();

            if ($find_category) {
                $total_rating['id'] = $find_category->id;
                $total_rating['average_rating'] = round($find_category->rating_sum / $find_category->rated_user);
                return $this->apiResponse->sendResponse(200, 'Get Category Rating Successfully', $total_rating);
            }

            return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_category_enrollment(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $find_category = Category::find($request->category_id);
            if ($find_category) {
                $already = CategoryUserEnrollment::where(['user_id' => Auth::user()->id, 'category_id' => $request->category_id])->first();
                if (!$already) {
                    Auth::user()->categoryEnrollment()->attach($request->category_id);
                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Category Enrollment Successfully', null);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(409, 'Already Enrolled Category', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_category_enrollment(Request $request)
    {
        DB::beginTransaction();

        try {
            $enrolled_categories = CategoryUserEnrollment::with('categories')->where('user_id', Auth::user()->id)->get();
            return $this->apiResponse->sendResponse(200, 'Get Enrolled Category Successfully', $enrolled_categories);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function getNextLevel(Request $request)
    {
        DB::beginTransaction();
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

        $categories = Category::with('user:id,name,avatar,slug')->where('level', $request->level)->where('parent_id', $parent_id)->get();
        if (count($categories) == 0) {
            $request->request->add(['category_id' => $parent_id]);
            return $this->get_learning_path($request);
        }
        DB::commit();
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
        if ($request->role_id && $request->role_id == 3)
            return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories = Category::get());
        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories = Category::where('visibility', 1)->get());
    }

    public function getAllCategoriesHierarchically(Request $request)
    {
        if ($request->role_id && $request->role_id == 3)
            $categories = Category::get();
        else
            $categories = Category::where('visibility', 1)->get();
        $tree = function ($elements, $parentId = 0) use (&$tree) {
            $branch = array();
            foreach ($elements as $element) {

                if ($element['parent_id'] == $parentId) {

                    $children = $tree($elements, $element['id']);
                    if ($children) {
                        $element['children'] = $children;
                    } else {
                        // $element['children'] = [];
                    }
                    $branch[] = $element;
                }
            }

            return $branch;
        };

        $tree = $tree($categories);
        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $tree);
    }


    public function getCategories(Request $request)
    {
        DB::beginTransaction();
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
        DB::commit();
        return $this->apiResponse->sendResponse(200, 'Categories fetched successfully', $categories);
    }

    ///// Start removeCategory Function /////
    public function removeCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {

            $category = Category::find($request->category_id);
            if (is_null($category)) {

                return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
            } else {
                if ($category->user_id == Auth::user()->id) {
                    $success = $this->deleteAllCategory($category->id);
                    $learning_paths = LearningPath::whereIn('category_id', $success)->delete();
                    Category::whereIn('id', $success)->delete();

                    return $this->apiResponse->sendResponse(200, 'Category deleted successfully', null);
                } else {
                    return $this->apiResponse->sendResponse(401, 'Unauthorized user can not delete category', null);
                }
            }
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    // for removeCategory Recursive function
    private function deleteAllCategory($id)
    {
        $theArray = array();

        $category = Category::find($id);
        array_push($theArray, $category->id);

        $toRecurses = Category::where('parent_id', $category->id)->get();
        foreach ($toRecurses as $toRecurse) {
            array_push($theArray, $toRecurse->id);
        }

        foreach ($toRecurses as $toRecurse) {
            if (Category::where('parent_id', $toRecurse->id)->get()) {
                $children = $this->deleteAllCategory($toRecurse->id);
                if ($children) {
                    $theArray[] = $children;
                }
            }
        }

        $theArray1 = $this->flatten($theArray);
        $filteredArray = array_unique($theArray1);
        $reversed = array_reverse($filteredArray);
        return $reversed;
    }

    // for deleteAllCategory Recursive function
    function flatten(array $array)
    {
        $return = array();
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });

        return $return;
    }

    ///// End removeCategory Function /////

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
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Need a resource Id', $validator->errors());
        }

        $num_likes = Video::where('url', $request->video_url)->first()->num_likes();
        // Send notification via Notification controller function or guzzle
        DB::commit();
        return $this->apiResponse->sendResponse(200, 'Success', $num_likes);
    }

    public function add_resource_comment(Request $request)
    {
        DB::beginTransaction();
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

        DB::commit();
        // Send notification via Notification controller function or guzzle
        return $this->apiResponse->sendResponse(200, 'Comment added successfully', $comment);
    }

    public function switch_video_like(Request $request)
    {
        DB::beginTransaction();
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
        DB::commit();
        return $this->apiResponse->sendResponse(200, 'Like Updated successfully', null);
    }

    public function addToWatchHistory(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (Auth::user()) {
                $start_time = strtotime($request->start_time);
                $end_time = strtotime($request->end_time);
                $video = Video::where('url', $request->video_url)->first();
                // $user = User::find(1);
                if (!$video) {
                    $video = new Video();
                    $video->url = $request->video_url;
                    $video->save();
                }
                Auth::user()->watchHistoryVidoes()->attach($video->id, ['start_time' => $start_time, 'end_time' => $end_time, 'type' => 'history']);

                $check_history = HistoryUserVidoes::where(['user_id' => Auth::user()->id, 'video_id' => $video->id])->get();
                if (count($check_history) == 1) {
                    Video::where('id', $video->id)->update(['total_view' => $video->total_view + 1]);
                }

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video saved to history', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function getWatchHistory()
    {
        DB::beginTransaction();
        try {
            $user_id = Auth::user()->id;
            $getHistory = Video::select('*')
                ->with('duration_history:video_id,start_time,end_time')
                ->whereHas('duration_history', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })->paginate();
            DB::commit();

            return $this->apiResponse->sendResponse(200, 'User watch history get successfully', $getHistory);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function getPublicHistory(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $user_id = $request->user_id;
            $check_privacy = User::where(['id' => $user_id, 'is_history_public' => 1])->first();
            if ($check_privacy) {
                $publicHistory = Video::select('*')
                    ->with('duration_history:video_id,start_time,end_time')
                    ->whereHas('duration_history', function ($query) use ($user_id) {
                        $query->where('user_id', $user_id);
                    })->paginate();
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'User public watch history get successfully', $publicHistory);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function change_history_public(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'is_public' => 'required|numeric|between:0,1',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $change_history_privacy = User::where('id', Auth::user()->id)->update(['is_history_public' => $request->is_public]);
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'History Privacy Chnaged Successfully', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_video_to_learning_path(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|int',
            'video_url' => 'required|string',
            'ordering' => 'required|int',
            'start_time' => 'integer'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->role->contains(2)) {
                $video = Video::where('url', $request->video_url)->first();
                if (!$video) {
                    $video = new Video(['url' => $request->video_url]);
                    $video->save();
                }

                $findAuthorizeUser = Category::where(['user_id' => Auth::user()->id, 'id' => $request->category_id])->first();
                if ($findAuthorizeUser) {
                    $check = LearningPath::with('video')->where(['category_id' => $request->category_id, 'video_id' => $video->id])->first();
                    if ($check) {
                        DB::commit();
                        return $this->apiResponse->sendResponse(409, 'Already Video Added to this Category', $check);
                    }

                    if ($request->ordering == -1) {
                        $lp = LearningPath::where('category_id', $request->category_id)->orderBy('ordering', 'desc')->first();
                        if ($lp)
                            $ordering = $lp->ordering + 1;
                        else
                            $ordering = 1;
                    } else {
                        $ordering = $request->ordering;
                    }

                    if ($request->start_time) {
                        $start_time = $request->start_time;
                    } else {
                        $start_time = 0;
                    }

                    $new_lp_id = LearningPath::create(['user_id' => Auth::user()->id, 'category_id' => $request->category_id, 'video_id' => $video->id, 'ordering' => $ordering, 'start_time' => $start_time]);

                    $video_time = youtube_video_time_get($request->video_url); // In seconds
                    $category_find = Category::find($request->category_id);
                    $category_find->video_count += 1;
                    $category_find->total_time += $video_time;
                    $category_find->save();

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Learning path updated', $new_lp_id);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(401, 'Unauthorize User', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Only Teacher can add video to learning path', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function change_video_ordering(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'video_url' => 'required|string',
            'new_order' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->role->contains(2)) {
                $searchVideo = Video::where('url', $request->video_url)->first();
                if ($searchVideo) {
                    $find_learning_path = LearningPath::where(['user_id' => Auth::user()->id, 'category_id' => $request->category_id, 'video_id' => $searchVideo->id])->first();
                    if ($find_learning_path) {
                        $query = LearningPath::where(['user_id' => Auth::user()->id, 'category_id' => $request->category_id])->where('video_id', '!=', $searchVideo->id);

                        if ($request->new_order > $find_learning_path->ordering) {
                            $query->where('ordering', '>', $find_learning_path->ordering)->where('ordering', '<=', $request->new_order)->decrement('ordering', 1);

                            $find_learning_path->ordering = $request->new_order;
                            $find_learning_path->save();
                        } else if ($request->new_order < $find_learning_path->ordering) {
                            $query->where('ordering', '<', $find_learning_path->ordering)->where('ordering', '>=', $request->new_order)->increment('ordering', 1);

                            $find_learning_path->ordering = $request->new_order;
                            $find_learning_path->save();
                        } else {
                            DB::commit();
                            return $this->apiResponse->sendResponse(409, 'Learning path already in this order', null);
                        }

                        DB::commit();
                        return $this->apiResponse->sendResponse(200, 'Learning path ordering successfully', null);
                    }
                    DB::commit();
                    return $this->apiResponse->sendResponse(404, 'Learning path not found', null);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Video Not Found', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Unauthorize user', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function remove_video_from_learning_path(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|int',
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            if (Auth::user()->role->contains(2)) {
                $findAuthorizeUser = Category::where(['user_id' => Auth::user()->id, 'id' => $request->category_id])->first();
                if ($findAuthorizeUser) {
                    $video_id = Video::where('url', $request->video_url)->first();
                    $check = LearningPath::where(['category_id' => $request->category_id, 'video_id' => $video_id->id])->first();

                    if (!$check) {
                        DB::commit();
                        return $this->apiResponse->sendResponse(404, 'Video in this Category Not Found', null);
                    }

                    $video_time = youtube_video_time_get($request->video_url);
                    $category_find = Category::find($request->category_id);
                    $category_find->video_count -= 1;
                    $category_find->total_time -= $video_time;
                    $category_find->save();

                    $check->delete();

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Video Removed From Learning Path', null);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(401, 'Unauthorize User', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(401, 'Only Teacher can remove video from learning path', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function give_video_rating(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_url' => 'required|string',
            'rating' => 'required|integer|between:1,5',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        try {
            if (Auth::user()) {
                $where_video = ['url' => $request->video_url];
                $insert_video = ['url' => $request->video_url];
                $video_save = Video::updateOrCreate($where_video, $insert_video);

                $where_rating = ['user_id' => Auth::user()->id, 'video_id' => $video_save->id];
                $insert_rating = ['rating' => $request->rating];
                $video_rating = VideoRating::updateOrCreate($where_rating, $insert_rating);

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video Rating added successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function user_bookmark_video(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'video_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (Auth::user()) {
                $video = BookmarkVideo::where('video_id', $request->video_id)->first();
                if (!$video) {
                    Auth::user()->bookmarkVideo()->attach($video->id);
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Video Bookmark successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function attempt_test(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|integer',
            'test_answer' => 'required', //array
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (Auth::user()) {
                $where_rating = ['user_id' => Auth::user()->id, 'test_id' => $request->test_id];
                $insert_rating = ['test_answer' => $request->test_answer];
                $attempt_test = AttemptTest::updateOrCreate($where_rating, $insert_rating);

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Test answers saved successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_image_to_category(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
            'image' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            $searchCategory = Category::where(['id' => $request->category_id, 'user_id' => Auth::user()->id]);
            if ($searchCategory && $request->file('image')) {

                $attachment = $request->file('image');
                $storage_path = 'category/images/';
                $imgpath = commonUploadFile($storage_path, $attachment);

                $category = Category::find($request->category_id);
                $category->image_url = env('BASE_URL') . $imgpath;
                $category->save();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Category image added successfully', $category);
            } else {
                return $this->apiResponse->sendResponse(401, 'Category Not Exits or Unauthorized User', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_keyword_to_video(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'keyword'   => 'required|string',
            'video_url' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (Auth::user()) {
                $keyword = Keyword::where('keyword', $request->keyword)->first();
                $video = Video::where('url', $request->video_url)->first();

                if (!$keyword) {
                    $keyword = new Keyword();
                    $keyword->keyword = $request->keyword;
                    $keyword->save();
                }
                if (!$video) {
                    $video = new Video();
                    $video->url = $request->video_url;
                    $video->save();
                }

                $keywordByUserExits = KeywordUser::where(['user_id' => Auth::user()->id, 'keyword_id' => $keyword->id])->first();
                $keywordOfVideoExits = KeywordVideo::where(['video_id' => $video->id, 'keyword_id' => $keyword->id])->first();

                if (!$keywordByUserExits && !$keywordOfVideoExits) {
                    Auth::user()->keywords()->attach($keyword->id);
                    $video->keywords()->attach($keyword->id);

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Keyword added successfully', null);
                } elseif (!$keywordByUserExits) {
                    Auth::user()->keywords()->attach($keyword->id);

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Keyword added successfully', null);
                } elseif (!$keywordOfVideoExits) {
                    $video->keywords()->attach($keyword->id);

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Keyword added successfully', null);
                } else {

                    return $this->apiResponse->sendResponse(409, 'Already keyword exits by you', null);
                }
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_keyword_to_category(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id'  => 'required|integer',
            'keyword'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $keyword = Keyword::where('keyword', strtolower($request->keyword))->first();
            $category = Category::find($request->category_id);

            if (!$category) {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
            } else {
                if (!$keyword) {
                    $keyword = new Keyword();
                    $keyword->keyword = strtolower($request->keyword);
                    $keyword->save();
                }
            }

            $checkRecord = DB::table('category_keyword')->where(['user_id' => Auth::user()->id, 'category_id' => $category->id, 'keyword_id' => $keyword->id])->exists();

            if ($checkRecord) {
                DB::Commit();
                return $this->apiResponse->sendResponse(409, 'Already added this Category', null);
            }

            $keyword->categories()->attach($category->id, array('user_id' => Auth::user()->id));
            DB::Commit();
            return $this->apiResponse->sendResponse(200, 'Keyword added to this Category', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_keywords_of_category(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id'  => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $category = Category::find($request->category_id);

            if (!$category) {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Category Not Found', null);
            }

            $keywords = $category->keywords()->groupBy('keyword')->get();
            DB::Commit();
            return $this->apiResponse->sendResponse(200, 'Keywords get successfully', $keywords);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function toggle_category_visibility(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $category = Category::find($request->category_id);

        $category->toggle_visibility();
        $category->save();

        return $this->apiResponse->sendResponse(200, 'Like Updated successfully', null);
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

    public function category_user_id_change_to_admin(Request $request)
    {
        DB::beginTransaction();

        try {
            //            if (Auth::user()->role_id == 3) {
            $change_user_id = Category::where('user_id', 0)->orWhere('user_id', null)->update(['user_id' => 1]);

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'User ID Changed Successfully', null);
            //            } else {
            //                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            //            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function video_annotataion_user_id_change_to_admin(Request $request)
    {
        DB::beginTransaction();
        try {
            //            if (Auth::user()->role_id == 3) {
            $change_user_id = VideoAnnotation::where('user_id', 0)->update(['user_id' => 1]);

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'User ID Changed Successfully', null);
            //            } else {
            //                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            //            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
