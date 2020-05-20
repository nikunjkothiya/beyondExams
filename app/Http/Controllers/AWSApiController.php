<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\FileType;
use App\Resource;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use FFMpeg;


class AWSApiController extends Controller
{
    private $apiResponse;
    private $file_parameters = ["url", "thumbnail", "type", "length", "title", "author", "designation", "profile_pic"];
    private $base_url = 'http://precisely-test1.s3.ap-south-1.amazonaws.com/';
    private $file_types = ["all", "articles/", "images/", "audios/", "videos/"];

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function save_thumbnail(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'file' => 'required',
            'title' => 'required|string',
            'type' => 'required|integer|min:1|max:' . FileType::count(),
        ]);
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
            if ($request->type == 0) {
                $all_files = Resource::all();
            } else {
                $all_files = Resource::with('user:id,name,avatar')->where('file_type_id', $request->type)->get();
            }

            foreach ($all_files as $file) {
                $file["file_url"] = $this->base_url . $file["file_url"];
            }

            return $this->apiResponse->sendResponse(200, 'Success', $all_files);

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', null);
        }
    }

    public function search_s3_files(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'keyword' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $all_files = Resource::with('user:id,name,avatar')->where(DB::raw('SOUNDEX(title) LIKE CONCAT("%", SOUNDEX({$request->keyword}), "%")'))->get();
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', null);
        }
    }

    public function store_s3_file(Request $request)
    {
        $file_parameters = ["url", "thumbnail", "type", "length", "title", "author"];

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'file' => 'required',
            'title' => 'required|string',
            'type' => 'required|integer|min:1|max:' . FileType::count(),
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user = User::find($request->user_id);

        if ($user->role_id != 3) {
            return $this->apiResponse->sendResponse(400, 'Not a mentor', null);
        }

        $file = $request->file('file');
        $ext = "." . pathinfo($_FILES["file"]["name"])['extension'];

        $name = time() . uniqid() . $ext;


        $contents = file_get_contents($file);

        $filePath = $this->file_types[$request->type] . $name;

        if ($request->type == 1) {
//            ARTICLES

            $word = str_word_count(strip_tags($contents));
            $m = floor($word / 200);
            $s = floor($word % 200 / (200 / 60));
            $duration = $m . ' minute' . ($m == 1 ? '' : 's') . ', ' . $s . ' second' . ($s == 1 ? '' : 's');

            Storage::disk('s3')->put($filePath, $contents);
        } else if ($request->type == 2) {
//            IMAGE
            Storage::disk('s3')->put($filePath, $contents);
            $duration = null;
        } else if ($request->type == 3) {
//            AUDIO
            Storage::putFileAs(
                'public/', $file,  $filePath
            );

            Storage::putFileAs(
                'public/', $file,  $filePath
            );

            Storage::disk('s3')->put($filePath, $contents);

            $ffprobe = FFMpeg\FFProbe::create(array(
                'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/bin/ffprobe'
            ));

            $duration = $ffprobe
                ->streams(storage_path('app/public/' . $filePath))
                ->videos()
                ->first()
                ->get('duration');
            $duration = null;
        } else  if ($request->type == 4) {
//            VIDEO
            Storage::putFileAs(
                'public/', $file,  $filePath
            );

            Storage::disk('s3')->put($filePath, $contents);

            $ffprobe = FFMpeg\FFProbe::create(array(
                'ffmpeg.binaries'  => '/usr/local/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/bin/ffprobe'
            ));

            $duration = $ffprobe
                ->streams(storage_path('app/public/' . $filePath))
                ->videos()
                ->first()
                ->get('duration');

        } else {
            return $this->apiResponse->sendResponse(400, 'File type not supported', null);
        }

//        return $this->apiResponse->sendResponse(200, 'Success', $filePath);

        $new_resource = new Resource();
        $new_resource->file_url = $filePath;
        $new_resource->file_type_id = $request->type;
        $new_resource->title = $request->title;
        $new_resource->author_id = $user->id;
        $new_resource->duration = $duration;
        $new_resource->save();

        return $this->apiResponse->sendResponse(200, 'Success', $this->base_url . $filePath);

    }
}
