<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Currency;
use App\FileType;
use App\Key;
use App\KeyPrice;
use App\MessageType;
use App\Note;
use App\Reply;
use App\Resource;
use App\ResourceComment;
use App\ResourceKey;
use App\ResourceLike;
use App\ResourceTimeline;
use App\Test;
use App\TestScore;
use App\Transaction;
use App\User;
use App\UserKey;
use App\UserLastLogin;
use http\Message;
use Illuminate\Http\Request;

use Razorpay\Api\Api;

use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ResourceController extends Controller
{
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function generate_resource_timeline(){
        $users = UserLastLogin::where('updated_at', '>', Carbon::now()->subWeek())->get();
        $resources = Resource::orderBy('updated_at', 'DESC')->limit(50)->get();
        $oldTimelines = ResourceTimeline::whereIn('user_id', $users);
        if(!is_null($oldTimelines->get()))
            $oldTimelines->delete();
        foreach($users as $user){
            $i = 1;
            foreach($resources as $resource){
                $timeline = new ResourceTimeline();
                $timeline->resource_id = $resource->id;
                $timeline->user_id = $user->id;
                $timeline->priority = $i;
                $timeline->save();
                $i = $i +1;
            }
        }
    }


    public function get_resource_comments(Request $request){
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Need a resource Id', $validator->errors());
        }

        $comments = ResourceComment::where('resource_id', $request->resource_id)->get();
        // Send notification via Notification controller function or guzzle
        return $this->apiResponse->sendResponse(200, 'Success', $comments);
    }

    public function add_resource_comment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'resource_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user = Auth::user();
        $resource = Resource::find($request->resource_id);
        if(is_null($resource))
            return $this->apiResponse->sendResponse(404, 'Resource not found.', null);

        $comment = new ResourceComment();
        $comment->message = $request->message;
        $comment->user_id = $user->id;
        $comment->resource_id = $resource->id;
        if(isset($request->is_child))
            $comment->is_child = $request->is_child;
        if(isset($request->message_type))
            $comment->message_type = $request->message_type;
        $comment->save();

        $resource->num_comments = $resource->num_comments + 1;
        $resource->save();
        // Send notification via Notification controller function or guzzle
        return $this->apiResponse->sendResponse(200, 'Comment added successfully', $comment);
    }

    public function add_resource_like(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user = Auth::user();
        $resource = Resource::find($request->resource_id);
        if(is_null($resource))
            return $this->apiResponse->sendResponse(404, 'Resource not found.', null);

        $liked = $resource->likes()->where('user_id', $user->id);

        if(!is_null($liked->first())){
            $liked->delete();
            $resource->num_likes = $resource->num_likes - 1;
            $resource->save();
            return $this->apiResponse->sendResponse(200, 'Like removed', null);
        }

        $resource->likes()->create([
            'user_id' => $user->id
        ]);

        $resource->num_likes = $resource->num_likes + 1;
        $resource->save();

        // Send notification via Notification controller function or guzzle

        return $this->apiResponse->sendResponse(200, 'Resource liked', null);
    }

    // TO be removed
    public function add_resource_reply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'resource_id' => 'required|integer',
            'comment_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user = Auth::user();
        $comment = Comment::find($request->comment_id);

        $reply = new Reply();
        $reply->message = $request->message;
        $reply->user_id = $user->id;


        $comment->replies()->save($reply);
        $reply->save();

        return $this->apiResponse->sendResponse(200, 'Comment added successfully', null);
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
            $resource_key = ResourceKey::where('key_id', $request->key_id)->first();

            if (!$resource_key) {
                return $this->apiResponse->sendResponse(400, 'Resource Key Does not exist', null);
            }

            if (!$payment) {
                return $this->apiResponse->sendResponse(400, 'Payment ID is invalid', null);
            }

            // Capture the payment
            if ($payment->status == 'authorized') {
                $key = ResourceKey::where('id', $request->key_id)->get();
                if(is_null($key))
                    return $this->apiResponse->sendResponse(404, 'Key Does not exist', null);

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
                    ['key_id' => $key->id, 'user_id' => Auth::user()->id]
                );

                $resource = Resource::where('id', $key->resource_id)->first();
                if($resource){
                    $resource->num_subscribers = $resource->num_subscribers + 1;
                    $resource->save();
                }

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
            'resource_id' => 'required|integer',
            'test_doc' => 'file|mimes:pdf',
            'test_image' => 'image'
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
            else if ($request->test_doc) {
                if ($resource) {
                    $file = $request->file('test_doc');

                    $ext = "." . pathinfo($_FILES["test_doc"]["name"])['extension'];

                    $name = time() . uniqid() . $ext;

                    $contents = file_get_contents($file);

                    $filePath = "tests/" . $name;

                    Storage::disk('s3')->put($filePath, $contents);

                    $test->url = $filePath;
                    $test->type_id = MessageType::where('type', 'document')->value('id');
                } else {
                    return $this->apiResponse->sendResponse(404, 'Resource doesnt exist', null);
                }
            } else if ($request->test_image) {

                if ($resource) {
                    $file = $request->file('test_image');

                    $ext = "." . pathinfo($_FILES["test_image"]["name"])['extension'];

                    $name = time() . uniqid() . $ext;

                    $contents = file_get_contents($file);

                    $filePath = "tests/" . $name;

                    Storage::disk('s3')->put($filePath, $contents);

                    $test->url = $filePath;
                    $test->type_id = MessageType::where('type', 'image')->value('id');

                    return $this->apiResponse->sendResponse(200, 'Test added successfully', null);
                } else {
                    return $this->apiResponse->sendResponse(404, 'Resource doesnt exist', null);
                }
            } else
                return $this->apiResponse->sendResponse(500, 'Question not found', null);

            $resource->tests()->save($test);
            $test->save();

            return $this->apiResponse->sendResponse(200, 'Test added successfully', null);
        }
    }

    public function get_test_scores(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user = Auth::user();
        if ($user->role()->value('is_mentor') != 1) {
            return $this->apiResponse->sendResponse(403, 'User is not authorized', null);
        } else {
            $test = Test::find($request->test_id);
            return $this->apiResponse->sendResponse(200, 'Test scores fetched successfully', $test->scores()->with('user')->get());
        }
    }

    public function submit_test_score(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'score' => 'required|integer',
            'test_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user = Auth::user();
        $test = Test::find($request->test_id);

        $test_score = new TestScore();
        $test_score->score = $request->score;
        $test_score->user_id = $user->id;

        $test->scores()->save($test_score);
        $test_score->save();

        return $this->apiResponse->sendResponse(200, 'Test score added successfully', null);
    }
}
