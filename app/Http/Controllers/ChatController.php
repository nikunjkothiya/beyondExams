<?php

namespace App\Http\Controllers;

use App\Chat;
use App\ChatMessage;
use App\MessageType;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


// Models

class ChatController extends Controller
{
    private $apiResponse;
    private $default_user_id = 35;
    private $user_role_id = 1;
    private $mentor_role_id = 2;
    private $admin_role_id = 3;
    private $organization_role_id = 4;
    private $num_entries_per_page = 15;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function get_all_chats(request $request)
    {
        try {

            $chats = Auth::user()->chats()->orderByDesc('updated_at')->paginate($this->num_entries_per_page);

            return $this->apiResponse->sendResponse(200, 'Success', $chats);

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_chat_messages(request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {

            if (!is_null(Auth::user()->chats()->where('chat_id', $request->chat_id)->first())) {
                $messages = ChatMessage::with(['sender' => function ($query) {
                    $query->select('id', 'name', 'avatar');
                }])->where('chat_id', $request->chat_id)->orderByDesc('created_at')->paginate($this->num_entries_per_page);
                return $this->apiResponse->sendResponse(200, 'Success', $messages);
            }
            return $this->apiResponse->sendResponse(403, 'Access to the chat is forbidden', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function create_chat(request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $chat = Auth::user()->chats()->where('receiver_id', $request->user_id)->first();

            if (is_null($chat)) {

                $chat = new Chat();
                $chat->creator_id = Auth::user()->id;
                $chat->title = $request->title;
                $chat->receiver_id = $request->user_id;
                $chat->save();

                $this->add_admin_message("Hey! How may I help you?", $chat->id, 3);

                $chat->users()->attach([Auth::user()->id, $request->user_id]);
            }


            return $this->apiResponse->sendResponse(200, 'Success', $chat);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_admin_message($message, $chat_id, $seconds)
    {
        $chat_message = new ChatMessage();
        $chat_message->message = $message;
        $chat_message->chat_id = $chat_id;
        $chat_message->sender_id = 1;
        $chat_message->created_at = Carbon::now()->subSeconds($seconds);
        $chat_message->updated_at = Carbon::now()->subSeconds($seconds);
        $chat_message->save();
    }

    public function create_support_chat()
    {

        try {
            $chat = Auth::user()->chats()->where('is_support', true)->first();
            if (is_null($chat)) {
                $chat = new Chat();
                $chat->title = "Precisely Support";
                $chat->creator_id = Auth::user()->id;
                $chat->receiver_id = 1;
                $chat->is_support = true;
                $chat->save();

                $chat->users()->attach([Auth::user()->id]);
            }

            return $this->apiResponse->sendResponse(200, 'Success', $chat);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function add_chat_user(request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'chat_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $chat = Chat::where('id', $request->chat_id)->first();
            if (is_null($chat)) {
                return $this->apiResponse->sendResponse(404, 'Chat does not exist.', null);
            }

            $chat->users()->attach([$request->user_id]);

            return $this->apiResponse->sendResponse(200, 'User added to chat', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function send_message(request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $chat = Chat::where('id', $request->chat_id)->first();
            if (is_null($chat)) {
                return $this->apiResponse->sendResponse(404, 'Chat does not exist.', null);
            }

            $chat_message = new ChatMessage();
            $chat_message->message = $request->message;
            $chat_message->chat_id = $request->chat_id;
            $chat_message->type_id = 1;
            $chat_message->sender_id = Auth::user()->id;
            $chat_message->save();

            $chat->updated_at = Carbon::now();
            $chat->save();

            // Send Notifications via get firebaseIDs

            $chat_message = ChatMessage::with(['sender' => function ($query) {
                $query->select('id', 'name', 'avatar', 'role_id');
            }])->find($chat_message->id);

            return $this->apiResponse->sendResponse(200, 'Message Added', $chat_message);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function send_multimedia_message(request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
            'file' => 'required',
            'type_id' => 'required|integer|min:2|max:' . MessageType::count(),
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $chat = Chat::where('id', $request->chat_id)->first();

            if (is_null($chat)) {
                return $this->apiResponse->sendResponse(404, 'Chat does not exist.', null);
            }

            $file = $request->file('file');
            $ext = "." . pathinfo($_FILES["file"]["name"])['extension'];

            $name = time() . uniqid() . $ext;

            $message_type = MessageType::find($request->type_id)["type"] . "/";
            $filePath = storage_path() . '/app/public/chats/' . $message_type;

            $file->move($filePath, $name);

            $chat_message = new ChatMessage();
            $chat_message->message = url('/storage/chats/' . $message_type . $name);
            $chat_message->chat_id = $request->chat_id;
            $chat_message->type_id = $request->type_id;
            $chat_message->sender_id = Auth::user()->id;
            $chat_message->save();

            // Send Notifications via get firebaseIDs

            return $this->apiResponse->sendResponse(200, 'Multimedia message Added', $chat_message);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function change_chat_title(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|int',
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $chat = Chat::find($request->chat_id);
        $chat->title = $request->title;
        $chat->save();

        return $this->apiResponse->sendResponse(200, 'Chat title changed successfully.', $chat);
    }
}
