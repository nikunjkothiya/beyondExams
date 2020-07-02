<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponse;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use App\Key;
use App\UserKey;
use App\ResourceKey;

class ResourceLockController extends Controller
{
    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function save_new_key(request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'author_id' => 'required|integer',
            // 'price_inr' => 'required',
            // 'price_usd' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $newKey = new Key();
            $newKey->name = $request->name;
            $newKey->author_id = $request->author_id;
            $newKey->save();

            // $newKey->key_price()->create(
            //     [
            //         'author_id' => $request->author_id,
            //         'price_inr' => $request->price_inr,
            //         'price_usd' => $request->price_usd
            //     ]
            // );
            return $this->apiResponse->sendResponse(200, 'Key Added Succesfully', $newKey);

        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e);
        }
    }

    public function lock_resource(request $request){
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required|integer',
            'key_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $resourceKey = new ResourceKey();
        $resourceKey->resource_id = $request->resource_id;
        $resourceKey->key_id = $request->key_id;
        $resourceKey->save();

        return $this->apiResponse->sendResponse(200, 'Resource Locked With Key', $resourceKey);
    }

    public function get_user_keys(request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        $user = UserKey::where('user_id',$request->user_id)->get();
        return $this->apiResponse->sendResponse(200, 'Done', $user);
    }

    public function get_author_keys(request $request){
        $validator = Validator::make($request->all(), [
            'author_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        
        $keys = Key::where('author_id',$request->author_id)->get();

        return $this->apiResponse->sendResponse(200, 'Successful', $keys);
    }
}
