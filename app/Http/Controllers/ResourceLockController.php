<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use Auth;
use App\Key;
use App\KeyPrice;
use App\Currency;
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
            'price' => 'required|integer',
            'currency' => 'required|integer|min:1|max:' . Currency::count()
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $newKey = new Key();
            $newKey->name = $request->name;
            $newKey->author_id = $request->author_id;
            $newKey->save();

            $newKey->key_price()->create(
                [
                    'price' => $request->price,
                    'currency_id' => $request->currency,
                ]
            );
            $cur =  Currency::where('id',$request->currency)->first();
            $newKey['price'] = $request->price;
            $newKey['currency'] = $cur->name;
            return $this->apiResponse->sendResponse(200, 'Key Added Succesfully', $newKey);

        } catch (Exception $e) {
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
        $keys = UserKey::where('user_id', Auth::user()->id)->get();
        foreach($keys as $key){
            $k = Key::where('id',$key->key_id)->first();
            $kp = KeyPrice::where('key_id',$key->key_id)->first();
            $cur =  Currency::where('id',$kp->currency_id)->first();

            $key['name'] = $k->name;
            $key['price'] = $kp->price;
            $key['currency'] = $cur->name;
        }
        return $this->apiResponse->sendResponse(200, 'Done', $keys);
    }

    public function get_author_keys(request $request){
        $validator = Validator::make($request->all(), [
            'author_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        
        $keys = Key::where('author_id',$request->author_id)->get();
        foreach($keys as $key){
            $kp = KeyPrice::where('key_id',$key['id'])->first();
            $cur =  Currency::where('id',$kp->currency_id)->first();

            $key['price'] = $kp->price;
            $key['currency'] = $cur->name;
        }
        return $this->apiResponse->sendResponse(200, 'Successful', $keys);
    }
}
