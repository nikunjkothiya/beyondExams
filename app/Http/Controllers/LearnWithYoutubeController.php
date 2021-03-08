<?php

namespace App\Http\Controllers;

use App\Category;
use App\Comment;
use App\Country;
use App\Language;
use App\LearningPath;
use App\Role;
use App\UserHistory;
use App\User;
use App\Video;
use App\BookmarkVideo;
use App\HistoryUserVidoes;
use Auth;
use Config;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Search;
use App\VideoRating;
use App\AttemptTest;
use App\Keyword;
use App\KeywordUser;
use App\KeywordVideo;
use App\UserCertificate;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
                    'college' => 'string|max:1024',
                    'age' => 'required|int',
                    'country' => 'required|integer|min:1|max:' . Country::count(),
                    'profile_link' => 'string',
                    'short_bio' => 'sometimes|string',
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

                if (isset($request->facebook_link)) {
                    $user->facebook_link = $request->facebook_link;
                }

                if (isset($request->instagram_link)) {
                    $user->instagram_link = $request->instagram_link;
                }

                if (isset($request->github_link)) {
                    $user->github_link = $request->github_link;
                }

                if (isset($request->twitter_url)) {
                    $user->twitter_url = $request->twitter_url;
                }

                if (isset($request->linkedin_url)) {
                    $user->linkedin_url = $request->linkedin_url;
                }

                if (isset($request->phone))
                    $user->phone = $request->phone;

                $user->language_id = Language::where('code', Config::get('app.locale'))->first()->id;
                $slug = str_replace(" ", "-", strtolower($request->name)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 3);
                $user->slug = $slug;
                $user->age = $request->age;
                $user->country_id = $request->country;
                $user->phone = $request->phone;
                $user->flag = 1;
                $user->save();

                if ($request->file('image')) {
                    foreach ($request->file('image') as $image) {
                        $attachment = $image;
                        $storage_path = '/user/certificates/';
                        $imgpath = commonUploadImage($storage_path, $attachment);

                        $user_certidicate = new UserCertificate();
                        $user_certidicate->user_id = $user->id;
                        $user_certidicate->image = $imgpath;
                        $user_certidicate->save();
                    }
                }

                return $this->apiResponse->sendResponse(200, 'User details saved', $user);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_certificate(Request $request)
    {
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'image' => 'required',
                    'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }

                foreach ($request->file('image') as $image) {
                    $attachment = $image;
                    $storage_path = '/user/certificates/';
                    $imgpath = commonUploadImage($storage_path, $attachment);

                    $user_certidicate = new UserCertificate();
                    $user_certidicate->user_id = Auth::user()->id;
                    $user_certidicate->image = $imgpath;
                    $user_certidicate->save();
                }

                return $this->apiResponse->sendResponse(200, 'User Certificates Added Successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_user_profile()
    {
        if (Auth::check()) {
            //$user = Auth::user();
            $user = User::with('certificates')->where('id', Auth::user()->id)->get();

            return $this->apiResponse->sendResponse(200, 'Successfully fetched user profile.', $user);
        } else {
            return $this->apiResponse->sendResponse(500, 'User profile not complete', null);
        }
    }

    public function add_user_facebook_link(Request $request)
    {
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'facebook_link' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = User::find(Auth::user()->id);
                $user_link->facebook_link = $request->facebook_link;
                $user_link->save();

                return $this->apiResponse->sendResponse(200, 'User Facebook Link Added Successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_instagram_link(Request $request)
    {
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'instagram_link' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = User::find(Auth::user()->id);
                $user_link->instagram_link = $request->instagram_link;
                $user_link->save();

                return $this->apiResponse->sendResponse(200, 'User Instagram Link Added Successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_github_link(Request $request)
    {
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'github_link' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = User::find(Auth::user()->id);
                $user_link->github_link = $request->github_link;
                $user_link->save();

                return $this->apiResponse->sendResponse(200, 'User GitHub Link Added Successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_twitter_url(Request $request)
    {
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'twitter_url' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = User::find(Auth::user()->id);
                $user_link->twitter_url = $request->twitter_url;
                $user_link->save();

                return $this->apiResponse->sendResponse(200, 'User Twitter Link Added Successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_user_linkedin_url(Request $request)
    {
        try {
            if (Auth::check()) {
                $validator = Validator::make($request->all(), [
                    'linkedin_url' => 'required|string',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                $user_link = User::find(Auth::user()->id);
                $user_link->linkedin_url = $request->linkedin_url;
                $user_link->save();

                return $this->apiResponse->sendResponse(200, 'User LinkedIn Link Added Successfully', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function addNewCategory(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'level' => 'required|integer',
            'parent_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {
            // if (Auth::user()->role() == Role::find(1))
            //     return $this->apiResponse->sendResponse(401, 'User unauthorised.', null);
            $category = Category::create(['user_id' => Auth::user()->id, 'title' => $request->title, 'level' => $request->level, 'parent_id' => $request->parent_id]);
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'New Category added', $category);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
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

    public function getAllCategoriesHierarchically(Request $request)
    {
        $categories = Category::get();
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

    ///// Start removeCategory Function /////
    public function removeCategory(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
        }

        try {

            $category = Category::find($request->category_id);
            if (is_null($category)) {
                DB::commit();
                return $this->apiResponse->sendResponse(201, 'Category Not Found', null);
            }else {
                if ($category->user_id == Auth::user()->id) {
                    $success = $this->deleteAllCategory($category->id);
                    $learning_paths = LearningPath::whereIn('category_id',$success)->delete();
                    Category::whereIn('id', $success)->delete();

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Category deleted successfully', null);
                } else {
                    return $this->apiResponse->sendResponse(401, 'Unauthorized user can not delete category', null);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
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
            $user_id = 1;
            //$user_id = Auth::user()->id;
            $getHistory = Video::select('*')
                ->with('duration_history:video_id,start_time,end_time')
                ->whereHas('duration_history', function ($query) use ($user_id) {
                    $query->where('user_id', $user_id);
                })->get();
            DB::commit();
            if (count($getHistory) > 0) {
                return $this->apiResponse->sendResponse(200, 'User watch history get successfully', $getHistory);
            } else {
                return $this->apiResponse->sendResponse(200, 'User watch history not found', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
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
        // $user = User::find(1);
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
            $searchCategory = Category::find($request->category_id);
            if ($searchCategory && $request->file('image')) {

                $attachment = $request->file('image');
                $storage_path = '/category/images/';
                $imgpath = commonUploadImage($storage_path, $attachment);

                $category = Category::find($request->category_id);
                $category->image_url = $imgpath;
                $category->save();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Category image added successfully', $category);
            } else {
                return $this->apiResponse->sendResponse(201, 'Category Not Exits', null);
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

                    return $this->apiResponse->sendResponse(200, 'Already keyword exits by you', null);
                }
            } else {
                return $this->apiResponse->sendResponse(401, 'User unauthorized', null);
            }
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

        $category->toggle_visibility()->save();

        return $this->apiResponse->sendResponse(200, 'Like Updated successfully', null);
    }
}
