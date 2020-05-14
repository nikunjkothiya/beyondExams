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
                $urls = [];

                foreach ($all_files as $file) {
                    $urls[] = $this->base_url . $file;
                }
                $processed = [];
                foreach ($urls as $url) {
                    $processed[] = str_replace(' ', '+', $url);
                }

                $data = [];
                foreach ($processed as $pro) {
                    $data[] = array('url' => $pro, 'thumbnail' => null, 'type' => null, 'length' => null,
                        'title' => null, 'author' => null, 'designation' => null, 'profile_pic' => null);
                }

                return $this->apiResponse->sendResponse(200, 'Success', $data);
//            }

            $all_files = Storage::disk('s3')->files($this->file_types[$request->type]);
            $urls = [];

            foreach ($all_files as $file) {
                $urls[] = $this->base_url . $file;
            }
            $processed = [];
            foreach ($urls as $url) {
                $processed[] = str_replace(' ', '+', $url);
            }

            $data = [];
            foreach ($processed as $pro) {
                $data[] = array('url' => $pro, 'thumbnail' => null, 'type' => null, 'length' => null,
                    'title' => null, 'author' => null, 'designation' => null, 'profile_pic' => null);
            }

            return $this->apiResponse->sendResponse(200, 'Success', $data);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', null);
        }
    }

    public function search_s3_files(Request $request)
    {
        try {
            $all_files = Storage::disk('s3')->files("/video");
            $urls = [];

            foreach ($all_files as $file) {
                $urls[] = 'precisely-test1.s3.ap-south-1.amazonaws.com/' . $file;
            }

            $splited = [];
            foreach ($urls as $url) {
                $splited[] = explode("video/", $url)[1];
            }

            $req_files = [];
            $keyword = strtolower($request->keyword);

            foreach ($splited as $spl) {
                $exists = strpos(strtolower($spl), $keyword);
                if ($exists !== false) {
                    $req_files[] = str_replace(' ', '+', $this->base_url . 'video/' . $spl);
                }
            }

            if (empty($req_files)) {
                $req_files[] = "Not Found";
            }

            return $this->apiResponse->sendResponse(200, 'Success', $req_files);
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


        if ($request->type == 1) {
//            TEXT

            $word = str_word_count(strip_tags($contents));
            $m = floor($word / 200);
            $s = floor($word % 200 / (200 / 60));
            $duration = $m . ' minute' . ($m == 1 ? '' : 's') . ', ' . $s . ' second' . ($s == 1 ? '' : 's');

            $filePath = 'articles/' . $name;
            Storage::disk('s3')->put($filePath, $contents);
        } else  if ($request->type == 2) {
//            VIDEO
            $filePath = 'videos/' . $name;

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

        } else if ($request->type == 3) {
//            IMAGE
            $filePath = 'images/' . $name;
            Storage::disk('s3')->put($filePath, $contents);
            $duration = null;
        } else if ($request->type == 4) {
//            AUDIO
            $filePath = 'audios/' . $name;

            Storage::putFileAs(
                'public/', $file,  $filePath
            );

            Storage::disk('s3')->put($filePath, $contents);
            $duration = null;
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
