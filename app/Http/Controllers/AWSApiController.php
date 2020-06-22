<?php

namespace App\Http\Controllers;

use App\Playlist;
use Auth;
use App\User;
use App\FileType;
use App\Resource;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use FFMpeg;


class AWSApiController extends Controller
{
    private $apiResponse;
    private $file_parameters = ["url", "thumbnail", "type", "length", "title", "author", "designation", "profile_pic"];
    private $base_url = 'https://precisely-test1.s3.ap-south-1.amazonaws.com/';
    private $file_types = ["all", "blogs/", "articles/", "videos/"];

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function save_thumbnail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'file' => 'required',
                'resource_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }
            $resource = Resource::find($request->resource_id);
            if ($resource) {
                $file = $request->file('file');

                $ext = "." . pathinfo($_FILES["file"]["name"])['extension'];


                $name = time() . uniqid() . $ext;


                $contents = file_get_contents($file);

                $filePath = "thumbnails/" . $name;

                Storage::disk('s3')->put($filePath, $contents);

                $resource->thumbnail_url = $filePath;
                $resource->save();
            } else {
                return $this->apiResponse->sendResponse(400, 'Resource does not exist', null);
            }

            return $this->apiResponse->sendResponse(200, 'Success', $this->base_url . $filePath);
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getStackTrace());
        }
    }

    public function get_resource_from_slug(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'slug' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $file = Resource::with('user:id,name,avatar')->where('slug', $request->slug)->get();
            if (count($file) == 0)
                return $this->apiResponse->sendResponse(404, 'Resource not found', null);

            if (!is_null($file[0]["thumbnail_url"]))
                $file[0]["thumbnail_url"] = $this->base_url . $file[0]["thumbnail_url"];


            if ($file[0]["file_type_id"] == 3)
                $file[0]["file_url"] = $this->base_url . $file[0]["file_url"];

            return $this->apiResponse->sendResponse(200, 'Success', $file);

        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTrace());
        }
    }

    public function list_s3_files(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'type' => 'required|integer|min:0|max:' . FileType::count(),
            'author_id' => 'integer'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (isset($request->author_id)){
                if ($request->type == 0) {
                    $all_files = Resource::with('user:id,name,avatar')->where('author_id', $request->author_id)->get();
                } else if ($request->type == 3){
                    $all_files = Resource::with('user:id,name,avatar')->whereIn('file_type_id', [3, 4])->where('author_id', $request->author_id)->get();
                } else {
                    $all_files = Resource::with('user:id,name,avatar')->where('file_type_id', $request->type)->where('author_id', $request->author_id)->get();
                }
            } else {
                if ($request->type == 0) {
                    $all_files = Resource::with('user:id,name,avatar')->get();
                } else if ($request->type == 3){
                    $all_files = Resource::with('user:id,name,avatar')->whereIn('file_type_id', [3, 4])->get();
                } else {
                    $all_files = Resource::with('user:id,name,avatar')->where('file_type_id', $request->type)->get();
                }
            }

            foreach ($all_files as $file) {
                if (!is_null($file["thumbnail_url"]))
                    $file["thumbnail_url"] = $this->base_url . $file["thumbnail_url"];
                if ($file["file_type_id"] == 3)
                    $file["file_url"] = $this->base_url . $file["file_url"];
            }

            return $this->apiResponse->sendResponse(200, 'Success', $all_files);

        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    public function search_s3_files(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'keyword' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $all_files = Resource::with('user:id,name,avatar')->where('title', 'like', "%{$request->keyword}%")->get();

            foreach ($all_files as $file) {
                if (!is_null($file["thumbnail_url"]))
                    $file["thumbnail_url"] = $this->base_url . $file["thumbnail_url"];
                if ($file["file_type_id"] == 3)
                    $file["file_url"] = $this->base_url . $file["file_url"];
            }

            return $this->apiResponse->sendResponse(200, 'Success', $all_files);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getTraceAsString());
        }
    }

    public function store_s3_file(Request $request)
    {
        try {
            $file_parameters = ["url", "thumbnail", "type", "length", "title", "author"];

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'title' => 'required|string',
                'description' => 'required|string',
                'type' => 'required|integer|min:1|max:' . FileType::count(),
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $user = User::find($request->user_id);

            if ($user->role_id != 3) {
                return $this->apiResponse->sendResponse(400, 'Not a mentor', null);
            }

            $contents = $request->description;
            if ($request->type == 3) {
                $file = $request->file('file');
                $ext = "." . pathinfo($_FILES["file"]["name"])['extension'];

                $name = time() . uniqid() . $ext;

                $filePath = $this->file_types[$request->type] . $name;
            } else {
                $filePath = null;
            }

            $slug = str_replace(" ", "-", strtolower($request->title)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);

            $new_resource = new Resource();
            $new_resource->file_type_id = $request->type;
            $new_resource->title = $request->title;
            $new_resource->author_id = $user->id;
            $new_resource->slug = $slug;
            $new_resource->file_url = $filePath;
            $new_resource->description = $contents;

            if ($request->type == 1) {
//            BLOGS

                $word = str_word_count(strip_tags($contents));
                $m = floor($word / 200);
                $s = floor($word % 200 / (200 / 60));
                $duration = $s + $m * 60;
//            $duration = $m . ' minute' . ($m == 1 ? '' : 's') . ', ' . $s . ' second' . ($s == 1 ? '' : 's');

                $new_resource->duration = $duration;
                $new_resource->save();

            } else if ($request->type == 2) {
//            ARTICLES
                $word = str_word_count(strip_tags($contents));
                $m = floor($word / 200);
                $s = floor($word % 200 / (200 / 60));
                $duration = $s + $m * 60;

                $new_resource->duration = $duration;
                $new_resource->save();

            } else if ($request->type == 3) {
                //            VIDEO
//            return $this->apiResponse->sendResponse(200, 'Success', storage_path() . 'app/public/videos/');
                $file->move(storage_path() . '/app/public/videos/', $name);
//                Storage::putFileAs(
//                    'public/', $file, $filePath
//                );

                Storage::disk('s3')->put($filePath, $contents);

                $ffprobe = FFMpeg\FFProbe::create(array(
                    'ffmpeg.binaries' => '/usr/bin/ffmpeg',
                    'ffprobe.binaries' => '/usr/bin/ffprobe'
                ));

                $duration = $ffprobe
                    ->streams(storage_path('app/public/' . $filePath))
                    ->videos()
                    ->first()
                    ->get('duration');

                $new_resource->duration = $duration;
                $new_resource->save();

            } else {
                return $this->apiResponse->sendResponse(400, 'File type not supported', null);
            }

            return $this->apiResponse->sendResponse(200, 'Success', $this->base_url . $filePath);
        } catch (\Exception $e) {

            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e);
        }
    }

    public function save_playlist(Request $request)
    {
        $request = json_decode($request->all()["data"]);

        $slug = str_replace(" ", "-", strtolower($request->title)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);
        $resource = new Resource();
        $resource->file_type_id = 4;
        $resource->title = $request->title;
        $resource->author_id = $request->user_id;
        $resource->slug = $slug;
        $resource->file_url = $request->url;
        $resource->description = "";
        $resource->duration = $request->num_videos;
        $resource->save();

        $playlist = new Playlist();
        $playlist->resource_id = $resource->id;
        $playlist->structure = $request->structure;
        $playlist->save();
    }

    public function get_recommendations(Request $request){
        try {
            $playlist = Playlist::where('resource_id', $request->origin)->first();
            return $this->apiResponse->sendResponse(200, 'Success', $playlist->structure);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTrace());
        }
    }
}
