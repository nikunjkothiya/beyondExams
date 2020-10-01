<?php

namespace App\Http\Controllers;

use App\Currency;
use App\FileType;
use App\Key;
use App\KeyPrice;
use App\MessageType;
use App\Note;
use App\Playlist;
use App\Resource;
use App\ResourceKey;
use App\User;
use App\UserKey;
use App\UserResource;
use App\UserRole;
use Auth;
use Exception;
use FFMpeg;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class AWSApiController extends Controller
{
    private $apiResponse;
    private $file_parameters = ["url", "thumbnail", "type", "length", "title", "author", "designation", "profile_pic"];
    private $base_url = 'https://precisely-test1221001-dev.s3.ap-south-1.amazonaws.com/';
    private $file_types = ["all", "blogs/", "articles/", "videos/", "playlist/", "live/", "misc/", "podcast/"];
    private $apiConsumer;
    private $resourceCollection;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
        $this->apiConsumer = new Client();
        $this->resourceCollection = Resource::with(['user:id,name,avatar', 'notes', 'tests', 'comments']);
    }

    public function upload_single_image(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file' => 'required|image',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }
            $aws_root = "public/";
            $file = $request->file('file');
            $ext = "." . pathinfo($_FILES["file"]["name"])['extension'];
            $name = time() . uniqid() . $ext;
            $contents = file_get_contents($file);
            $filePath = "thumbnails/" . $name;
            Storage::disk('s3')->put($aws_root . $filePath, $contents);

            return $this->apiResponse->sendResponse(200, 'Success', $this->base_url . $aws_root . $filePath);
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e);
        }
    }

    public function save_thumbnail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'file' => 'required|image',
                'resource_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $aws_root = "public/";

            $resource = Resource::find($request->resource_id);
            if ($resource) {
                $file = $request->file('file');
                $ext = "." . pathinfo($_FILES["file"]["name"])['extension'];
                $name = time() . uniqid() . $ext;
                $contents = file_get_contents($file);
                $filePath = "thumbnails/" . $name;
                Storage::disk('s3')->put($aws_root . $filePath, $contents);
                $resource->thumbnail_url = $filePath;
                $resource->save();
            } else {
                return $this->apiResponse->sendResponse(400, 'Resource does not exist', null);
            }

            return $this->apiResponse->sendResponse(200, 'Success', $filePath);
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e);
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

            /* $user = Auth::user();*/

            $file = $this->resourceCollection->where('slug', $request->slug)->get();
            if (count($file) == 0)
                return $this->apiResponse->sendResponse(404, 'Resource not found', null);



            /*  $file['unlocked'] = false;
                $user_keys = UserKey::where('user_id', $user->user_id)->get();
                $keys = ResourceKey::where('resource_id', $file->id)->get();
                if (count($keys) === 0) {
                    $file['unlocked'] = true;
                }
                if ($keys) {
                    foreach ($keys as $key) {
                        foreach ($user_keys as $user_key) {
                            if ($user_key->key_id === $key->key_id) {
                                $file['unlocked'] = true;
                            }
                        }
                        unset($key['id']);
                        unset($key['resource_id']);
                        $k = Key::where('id', $key->key_id)->first();
                        $kp = KeyPrice::where('key_id', $key->key_id)->first();
                        $cur = Currency::where('id', $kp->currency_id)->first();

                        $key['name'] = $k->name;
                        $key['price'] = $kp->price;
                        $key['currency'] = $cur->name;
                    }
                }
                $file['keys'] = $keys;
            */

            /*            if (!is_null($file[0]["thumbnail_url"]))
                $file[0]["thumbnail_url"] = $this->base_url . $file[0]["thumbnail_url"];


                        if ($file[0]["file_type_id"] == 3)
                            $file[0]["file_url"] = $this->base_url . $file[0]["file_url"];
            */
            return $this->apiResponse->sendResponse(200, 'Success', $file);
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
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
            $flag = 2;
            $user_resources = UserResource::where('user_id', $request->user_id)->get();
            if (count($user_resources) === 0) {
                // User has 2 free videos
                $flag = 0;
            } elseif (count($user_resources) === 1) {
                // User has 1 free video
                $flag = 1;
            }
            if (isset($request->author_id)) {
                if ($request->type == 0) {
                    $all_files = $this->resourceCollection->where('author_id', $request->author_id)->where('duration', '>', 0)->orderBy('id', 'DESC')->get();
                } else if ($request->type == 3) {
                    $all_files = $this->resourceCollection->whereIn('file_type_id', [3, 4])->where('duration', '>', 0)->where('author_id', $request->author_id)->orderBy('id', 'DESC')->get();
                } else if ($request->type == 7) {
                    $all_files = $this->resourceCollection->whereIn('file_type_id', [7])->where('duration', '>', 0)->where('author_id', $request->author_id)->orderBy('id', 'DESC')->get();
                } else {
                    $all_files = $this->resourceCollection->where('file_type_id', $request->type)->where('author_id', $request->author_id)->orderBy('id', 'DESC')->get();
                }
            } else {
                if ($request->type == 0) {
                    $all_files = $this->resourceCollection->where('duration', '>', 0)->orderBy('id', 'DESC')->get();
                } else if ($request->type == 3) {
                    $all_files = $this->resourceCollection->whereIn('file_type_id', [3, 4])->where('duration', '>', 0)->orderBy('id', 'DESC')->get();
                } else if ($request->type == 7) {
                    $all_files = $this->resourceCollection->whereIn('file_type_id', [7])->where('duration', '>', 0)->orderBy('id', 'DESC')->get();
                } else {
                    $all_files = $this->resourceCollection->where('file_type_id', $request->type)->orderBy('id', 'DESC')->get();
                }
            }

            // foreach ($all_files as $file) {
            //     foreach ($file["notes"] as $note) {
            //         $note["url"] = $this->base_url . $note["url"];
            //     }
            //     foreach ($file["tests"] as $test) {
            //         $test["url"] = $this->base_url . $test["url"];
            //     }
            //     if (!is_null($file["thumbnail_url"]))
            //         $file["thumbnail_url"] = $this->base_url . $file["thumbnail_url"];
            //     if ($file["file_type_id"] == 3 || $file["file_type_id"] == 5)
            //         $file["file_url"] = $this->base_url . $file["file_url"];
            // }

            $resp['flag'] = $flag;


            foreach ($all_files as $file) {
                $file['unlocked'] = false;
                $user_keys = UserKey::where('user_id', $request->user_id)->get();
                $keys = ResourceKey::where('resource_id', $file->id)->get();
                if (count($keys) === 0) {
                    $file['unlocked'] = true;
                }
                if ($keys) {
                    foreach ($keys as $key) {
                        foreach ($user_keys as $user_key) {
                            if ($user_key->key_id === $key->key_id) {
                                $file['unlocked'] = true;
                            }
                        }
                        unset($key['id']);
                        unset($key['resource_id']);
                        $k = Key::where('id', $key->key_id)->first();
                        $kp = KeyPrice::where('key_id', $key->key_id)->first();
                        $cur = Currency::where('id', $kp->currency_id)->first();

                        $key['name'] = $k->name;
                        $key['price'] = $kp->price;
                        $key['currency'] = $cur->name;
                    }
                }
                if ($file->author_id == $request->user_id) {
                    $file['unlocked'] = true;
                }
                $file['keys'] = $keys;
            }

            $resp['data'] = $all_files;
            return $this->apiResponse->sendResponse(200, 'Success', $resp);
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_resource_stack(Request $request)
    {
        try {
            $all_files = $this->resourceCollection
                ->whereIn('file_type_id', [3, 4])->where('duration', '>', 0)
                ->orderBy('id', 'DESC')->take(9)->get();


            foreach ($all_files as $file) {
                $file['unlocked'] = false;
                $keys = ResourceKey::where('resource_id', $file->id)->get();
                if (count($keys) === 0) {
                    $file['unlocked'] = true;
                } else {
                    foreach ($keys as $key) {
                        unset($key['id']);
                        unset($key['resource_id']);
                        $k = Key::where('id', $key->key_id)->first();
                        $kp = KeyPrice::where('key_id', $key->key_id)->first();
                        $cur = Currency::where('id', $kp->currency_id)->first();

                        $key['name'] = $k->name;
                        $key['price'] = $kp->price;
                        $key['currency'] = $cur->name;
                    }
                }
                $file['keys'] = $keys;
            }

            $resp['data'] = $all_files;
            return $this->apiResponse->sendResponse(200, 'Success', $resp);
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function list_paginated_s3_files(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'type' => 'required|integer|min:0|max:' . FileType::count(),
            'author_id' => 'integer',
            'keyword' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $per_page = 10;
        $flag = 2;

        try {
            if ($request->keyword) {
                $all_files = $this->resourceCollection->where('title', 'like', "%{$request->keyword}%")->orderBy('id', 'DESC')->paginate($per_page);;
            } else {
                $user_resources = UserResource::where('user_id', $request->user_id)->get();
                if (count($user_resources) === 0) {
                    // User has 2 free videos
                    $flag = 0;
                } elseif (count($user_resources) === 1) {
                    // User has 1 free video
                    $flag = 1;
                }

                $resp['flag'] = $flag;

                if (isset($request->author_id)) {
                    if ($request->type == 0) {
                        $all_files = $this->resourceCollection->where('author_id', $request->author_id)->where('duration', '>', 0)->orderBy('id', 'DESC')->paginate($per_page);
                    } else if ($request->type == 3) {
                        $all_files = $this->resourceCollection->whereIn('file_type_id', [3, 4])->where('duration', '>', 0)->where('author_id', $request->author_id)->orderBy('id', 'DESC')->paginate($per_page);
                    } else if ($request->type == 7) {
                        $all_files = $this->resourceCollection->whereIn('file_type_id', [7])->where('duration', '>', 0)->where('author_id', $request->author_id)->orderBy('id', 'DESC')->paginate($per_page);
                    } else {
                        $all_files = $this->resourceCollection->where('file_type_id', $request->type)->where('author_id', $request->author_id)->orderBy('id', 'DESC')->paginate($per_page);
                    }
                } else {
                    if ($request->type == 0) {
                        $all_files = $this->resourceCollection->where('duration', '>', 0)->orderBy('id', 'DESC')->paginate($per_page);
                    } else if ($request->type == 3) {
                        $all_files = $this->resourceCollection->whereIn('file_type_id', [3, 4])->where('duration', '>', 0)->orderBy('id', 'DESC')->paginate($per_page);
                    } else if ($request->type == 7) {
                        $all_files = $this->resourceCollection->whereIn('file_type_id', [7])->where('duration', '>', 0)->orderBy('id', 'DESC')->paginate($per_page);
                    } else {
                        $all_files = $this->resourceCollection->where('file_type_id', $request->type)->orderBy('id', 'DESC')->paginate($per_page);
                    }
                }
            }


            foreach ($all_files as $file) {
                $file['unlocked'] = false;
                $user_keys = UserKey::where('user_id', $request->user_id)->get();
                $keys = ResourceKey::where('resource_id', $file->id)->get();
                if (count($keys) === 0) {
                    $file['unlocked'] = true;
                }
                if ($keys) {
                    foreach ($keys as $key) {
                        foreach ($user_keys as $user_key) {
                            if ($user_key->key_id === $key->key_id) {
                                $file['unlocked'] = true;
                            }
                        }
                        unset($key['id']);
                        unset($key['resource_id']);
                        $k = Key::where('id', $key->key_id)->first();
                        $kp = KeyPrice::where('key_id', $key->key_id)->first();
                        $cur = Currency::where('id', $kp->currency_id)->first();

                        $key['name'] = $k->name;
                        $key['price'] = $kp->price;
                        $key['currency'] = $cur->name;
                    }
                }
                $file['keys'] = $keys;
            }

            $resp['data'] = $all_files;
            return $this->apiResponse->sendResponse(200, 'Success', $resp);
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
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

            $all_files = $this->resourceCollection->where('title', 'like', "%{$request->keyword}%")->get();

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
                'notes_doc' => 'file|mimes:pdf',
                'notes_title' => 'string',
                'notes_image' => 'image',
                'price' => 'between:0,999.999',
                'currency_id' => 'integer|min:1|max:' . Currency::count()
            ]);

            $aws_root = "public/";

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $user = User::find($request->user_id);
            $user_id = $user->id;
            $newRole = UserRole::where('user_id', $user_id)->first();
            if ($newRole == null && $user->role_id === 1) {
                return $this->apiResponse->sendResponse(400, 'Not a mentor 1', null);
            }
            if ($newRole && $newRole->is_mentor != 1) {
                return $this->apiResponse->sendResponse(400, 'Not a mentor 2', null);
            }


            $contents = $request->description;
            if ($request->type == 3 || $request->type == 5 || $request->type == 7) {
                $file = $request->file('file');
                $ext = "." . pathinfo($_FILES["file"]["name"])['extension'];

                $name = time() . uniqid();

                $filePath = $this->file_types[$request->type] . $name;
                $file->move(storage_path() . '/app/public/' . $this->file_types[$request->type], $name . $ext);
                // shell_exec("ffmpeg -i " . storage_path('app/public/' . $filePath . $ext) . " " . storage_path('app/public/' . $filePath . ".mp4"));
                $filePath = $filePath . $ext;
                $name = $name . $ext;
            } else {
                $filePath = null;
            }

            $slug = str_replace(" ", "-", strtolower($request->title)) . "-" . substr(hash('sha256', mt_rand() . microtime()), 0, 16);

            $new_resource = new Resource();
            $new_resource->file_type_id = $request->type;
            $new_resource->title = $request->title;
            $new_resource->author_id = $user_id;
            $new_resource->slug = $slug;
            $new_resource->file_url = $filePath;
            $new_resource->description = $contents;

            if ($request->type == 1) {
                // BLOGS

                $word = str_word_count(strip_tags($contents));
                $m = floor($word / 200);
                $s = floor($word % 200 / (200 / 60));
                $duration = $s + $m * 60;
                if ($duration == 0 && $word > 3)
                    $duration = 1;
                // $duration = $m . ' minute' . ($m == 1 ? '' : 's') . ', ' . $s . ' second' . ($s == 1 ? '' : 's');

                if (is_null($duration) || $duration == 0)
                    return $this->apiResponse->sendResponse(400, 'File content not valid', null);

                $new_resource->duration = $duration;
                $new_resource->save();
            } else if ($request->type == 2) {
                // ARTICLES
                $word = str_word_count(strip_tags($contents));
                $m = floor($word / 200);
                $s = floor($word % 200 / (200 / 60));
                $duration = $s + $m * 60;

                if (is_null($duration) || $duration == 0)
                    return $this->apiResponse->sendResponse(400, 'File content not valid', null);

                $new_resource->duration = $duration;
                $new_resource->save();
            } else if ($request->type == 3 || $request->type == 5 || $request->type == 7) {
                // VIDEO 

                // $contents = Storage::get('public/videos/', $name);
                // Storage::putFileAs(
                // 'public/', $file, $filePath
                // );
                // Storage::disk('s3')->put($filePath, $contents);

                Storage::disk('s3')->put($aws_root . $filePath, file_get_contents(storage_path() . '/app/public/' . $this->file_types[$request->type] . $name));

                $ffprobe = FFMpeg\FFProbe::create(array(
                    'ffmpeg.binaries' => '/usr/bin/ffmpeg',
                    'ffprobe.binaries' => '/usr/bin/ffprobe'
                ));

                if ($ext == ".webm"){
                    $duration = 46;
                }
                else if ($ext == ".mp3"){
                    $duration = $ffprobe
                    ->streams(storage_path('app/public/' . $filePath))
                    ->audios()
                    ->first()
                    ->get('duration');
                } else {
                    $duration = $ffprobe
                        ->streams(storage_path('app/public/' . $filePath))
                        ->videos()
                        ->first()
                        ->get('duration');
                }

                // $duration = 180;

                if (is_null($duration) || $duration == 0)
                    $duration = 47;
                // return $this->apiResponse->sendResponse(400, 'File content not valid', null);

                $new_resource->duration = $duration;
                $new_resource->save();
                // WebmToMp4::dispatch();

            } else if ($request->type == 6) {
                Storage::disk('s3')->put($aws_root . $filePath, file_get_contents(storage_path() . '/app/public/misc/' . $name));
                $new_resource->duration = 1;
                $new_resource->save();
            } else {
                return $this->apiResponse->sendResponse(400, 'File type not supported', null);
            }

            if ($request->thumbnail) {
                $config = app()->make('config');
                $this->apiConsumer->post(sprintf('%s/api/v1/save_resource_thumbnail', $config->get('app.url')), [
                    'multipart' => [
                        [
                            'name' => 'user_id',
                            'contents' => $user_id
                        ],
                        [
                            'name' => 'file',
                            'contents' => file_get_contents($request->thumbnail),
                            'filename' => 'thumbnail.png'
                        ],
                        [
                            'name' => 'resource_id',
                            'contents' => $new_resource->id
                        ]
                    ]
                ]);
            }

            if ($request->notes_doc) {

                if ($new_resource) {
                    $file = $request->file('notes_doc');

                    $ext = "." . pathinfo($_FILES["notes_doc"]["name"])['extension'];


                    $name = time() . uniqid() . $ext;


                    $contents = file_get_contents($file);

                    $filePath = "notes/" . $name;

                    Storage::disk('s3')->put($aws_root . $filePath, $contents);

                    $note = new Note();
                    $note->url = $filePath;
                    $note->type_id = MessageType::where('type', 'document')->value('id');
                    if ($request->notes_title)
                        $note->title = $request->notes_title;
                    $new_resource->notes()->save($note);

                    $note->save();
                }
            } else if ($request->notes_image) {

                if ($new_resource) {
                    $file = $request->file('notes_image');

                    $ext = "." . pathinfo($_FILES["notes_image"]["name"])['extension'];

                    $name = time() . uniqid() . $ext;

                    $contents = file_get_contents($file);

                    $filePath = "notes/" . $name;

                    Storage::disk('s3')->put($aws_root . $filePath, $contents);

                    $note = new Note();
                    $note->url = $filePath;
                    $note->type_id = MessageType::where('type', 'image')->value('id');
                    if ($request->notes_title)
                        $note->title = $request->notes_title;
                    $new_resource->notes()->save($note);
                    $note->save();
                }
            }

            if ($request->price) {

                $newKey = new Key();
                $newKey->name = $request->title;
                $newKey->author_id = $user_id;
                $newKey->save();

                $newKey->key_price()->create(
                    [
                        'price' => $request->price,
                        'currency_id' => $request->currency_id,
                    ]
                );

                $resourceKey = new ResourceKey();
                $resourceKey->resource_id = $new_resource->id;
                $resourceKey->key_id = $newKey->id;
                $resourceKey->save();
            }

            return $this->apiResponse->sendResponse(200, 'Success', $this->resourceCollection->find($new_resource->id));
        } catch (\Exception $e) {

            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
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

    public function get_recommendations(Request $request)
    {
        try {
            $playlist = Playlist::where('resource_id', $request->origin)->first();
            return $this->apiResponse->sendResponse(200, 'Success', $playlist->structure);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
