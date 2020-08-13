<?php

namespace App\Http\Controllers;

use App\Currency;
use App\FileType;
use App\Key;
use App\KeyPrice;
use App\MessageType;
use App\Note;
use App\Resource;
use App\ResourceKey;
use App\Test;
use App\Transaction;
use App\User;
use App\UserKey;
use http\Message;
use Illuminate\Http\Request;

use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function save_new_key(request $request)
    {
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
            $cur = Currency::where('id', $request->currency)->first();
            $newKey['price'] = $request->price;
            $newKey['currency'] = $cur->name;
            return $this->apiResponse->sendResponse(200, 'Key Added Succesfully', $newKey);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function lock_resource(request $request)
    {
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

    public function get_user_keys(request $request)
    {
        $keys = UserKey::where('user_id', Auth::user()->id)->get();
        foreach ($keys as $key) {
            $k = Key::where('id', $key->key_id)->first();
            $kp = KeyPrice::where('key_id', $key->key_id)->first();
            $cur = Currency::where('id', $kp->currency_id)->first();

            $key['name'] = $k->name;
            $key['price'] = $kp->price;
            $key['currency'] = $cur->name;

            $resources = ResourceKey::where('key_id', $k->id)->get();
            foreach ($resources as $resource) {
                unset($resource['key_id']);
                unset($resource['id']);
                $resource_info = Resource::where('id', $resource->resource_id)->first();
                $author = User::where('id', $resource_info->author_id)->first();
                $resource['name'] = $resource_info->title;
                $resource['author'] = $author->name;
            }

            $key['resources'] = $resources;
        }
        return $this->apiResponse->sendResponse(200, 'Done', $keys);
    }

    public function get_author_keys(request $request)
    {
        $validator = Validator::make($request->all(), [
            'author_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $keys = Key::where('author_id', $request->author_id)->get();
        foreach ($keys as $key) {
            $kp = KeyPrice::where('key_id', $key['id'])->first();
            $cur = Currency::where('id', $kp->currency_id)->first();

            $key['price'] = $kp->price;
            $key['currency'] = $cur->name;
        }
        return $this->apiResponse->sendResponse(200, 'Successful', $keys);
    }

    function resource_checkout(request $request)
    {
        $validator = Validator::make($request->all(), [
            "payment_id" => "required",
            "key_id" => "required"
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {

            // Set Variables
            $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));

            // Get Payment Details
            $payment = $api->payment->fetch($request->payment_id);

            // Check if resource_key exist
            $resource_key = ResourceKey::where('id', $request->key_id)->first();

            if (!$resource_key) {
                return $this->apiResponse->sendResponse(400, 'Resource Key Does not exist', null);
            }

            if (!$payment) {
                return $this->apiResponse->sendResponse(400, 'Payment ID is invalid', null);
            }

            // Capture the payment
            if ($payment->status == 'authorized') {
                // Capturing Payment
                $payment->capture(
                    array('amount' => $payment->amount, 'currency' => $payment->currency)
                );
                // Create A TXN
                $txn = new Transaction();
                $txn->transaction_id = $payment->id;
                $txn->user_id = Auth::user()->id;
                $txn->product_id = 2;
                $txn->valid = 1;
                $txn->save();

                $txn->user_key()->create(
                    ['key_id' => $request->key_id, 'user_id' => Auth::user()->id]
                );

                return $this->apiResponse->sendResponse(200, 'Purchase Successful. Key Added', null);
            } else if ($payment->status == 'refunded') {
                // Payment was refunded
                return $this->apiResponse->sendResponse(400, 'Transaction was refunded', null);
            } else if ($payment->status == 'failed') {
                // Payment Failed
                return $this->apiResponse->sendResponse(400, 'Transaction was failed', null);
            } else if ($payment->status == 'captured') {
                // Payment Token Already used
                return $this->apiResponse->sendResponse(400, 'Transaction was already captured', null);
            } else {
                // Unkown Error
                return $this->apiResponse->sendResponse(400, 'Transaction not captured', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(400, 'Payment Error', $e->getMessage());
        }
    }

    function upload_notes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'notes_doc' => 'file|mimes:pdf',
            'notes_image' => 'image',
            'resource_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        if (Resource::find($request->resource_id)->user()->value('id') != Auth::user()->id) {
            return $this->apiResponse->sendResponse(403, 'User is not authorized', null);
        } else {
            $resource = Resource::find($request->resource_id);
            if ($request->notes_doc) {
                if ($resource) {
                    $file = $request->file('notes_doc');

                    $ext = "." . pathinfo($_FILES["notes_doc"]["name"])['extension'];


                    $name = time() . uniqid() . $ext;


                    $contents = file_get_contents($file);

                    $filePath = "notes/" . $name;

                    Storage::disk('s3')->put($filePath, $contents);

                    $note = new Note();
                    $note->url = $filePath;
                    $note->type_id = MessageType::where('type', 'document')->value('id');
                    if ($request->title)
                        $note->title = $request->title;

                    $resource->notes()->save($note);
                    $note->save();


                    return $this->apiResponse->sendResponse(200, 'Note added successfully', null);
                } else {
                    return $this->apiResponse->sendResponse(404, 'Resource doesnt exist', null);
                }
            } else if ($request->notes_image) {

                if ($resource) {
                    $file = $request->file('notes_image');

                    $ext = "." . pathinfo($_FILES["notes_image"]["name"])['extension'];

                    $name = time() . uniqid() . $ext;

                    $contents = file_get_contents($file);

                    $filePath = "notes/" . $name;

                    Storage::disk('s3')->put($filePath, $contents);

                    $note = new Note();
                    $note->url = $filePath;
                    $note->type_id = MessageType::where('type', 'image')->value('id');
                    if ($request->title)
                        $note->title = $request->title;
                    $resource->notes()->save($note);
                    $note->save();
                    return $this->apiResponse->sendResponse(200, 'Note added successfully', null);
                } else {
                    return $this->apiResponse->sendResponse(404, 'Resource doesnt exist', null);
                }
            }
        }

        return $this->apiResponse->sendResponse(500, 'Could not add note', null);
    }

    function upload_test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'resource_id' => 'required|integer'
//            'json_content'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        if (Resource::find($request->resource_id)->user()->value('id') != Auth::user()->id) {
            return $this->apiResponse->sendResponse(403, 'User is not authorized', null);
        } else {
            $resource = Resource::find($request->resource_id);

            $test = new Test();
            $test->title = $request->title;
            if ($request->json_content)
                $test->json_content = $request->json_content;
            else
                return $this->apiResponse->sendResponse(500, 'Question not found', null);

            $resource->tests()->save($test);
            $test->save();

            return $this->apiResponse->sendResponse(200, 'Test added successfully', null);

        }
    }
}
