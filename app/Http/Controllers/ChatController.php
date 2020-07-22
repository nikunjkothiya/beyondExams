<?php

namespace App\Http\Controllers;

use App\AdminFirebase;
use App\Http\Controllers\ApiResponse;
use App\MessageType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use Auth;
use GuzzleHttp\Client;

// Models
use App\Role;
use App\UserRole;
use App\Chat;
use App\ChatGroup;
use App\ChatMessage;
use App\ChatOperator;
use App\ChatUser;
use App\Opportunity;
use App\OpportunityRepresentative;
use App\StudentFirebase;

class ChatController extends Controller
{
    private $apiResponse;
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
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|min:1|max:' . Role::count()
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $user_role = UserRole::where('user_id', Auth::user()->id)->first();

            switch ($request->role_id) {
                case $this->user_role_id:
                    $chats = Auth::user()->chats()->where('is_support', false)->orderByDesc('created_at')->paginate($this->num_entries_per_page);
                    foreach ($chats as $chat) {
                        if (!$chat["is_group"])
                            $chat["group_id"] = ChatGroup::where("opportunity_id", $chat["opportunity_id"])->value("chat_id");
                        else
                            $chat["group_id"] = null;
                    }
                    // Requested Role is User
                    if ($user_role->is_user == 1) {
                        return $this->apiResponse->sendResponse(200, 'Success', $chats);
                    } else {
                        return $this->apiResponse->sendResponse(400, 'User is not a student.', null);
                    }
                    break;
                case $this->mentor_role_id:
                    $chats = Auth::user()->chats()->orderByDesc('created_at')->paginate($this->num_entries_per_page);
                    // Requested Role is Mentor
                    if ($user_role->is_mentor == 1) {
                        return $this->apiResponse->sendResponse(200, 'Success', $chats);
                    } else {
                        return $this->apiResponse->sendResponse(400, 'User is not a mentor.', null);
                    }
                    break;
                case $this->admin_role_id:
                    // Requested Role is Mentor
                    if ($user_role->is_admin == 1) {
                        $chats = Chat::all()->orderByDesc('created_at')->paginate($this->num_entries_per_page);
                        return $this->apiResponse->sendResponse(200, 'Success', $chats);
                    } else {
                        return $this->apiResponse->sendResponse(400, 'User is not a admin.', null);
                    }
                    break;
            }

            return $this->apiResponse->sendResponse(400, 'No such user role.', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function get_chat_messages(request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
            'role_id' => 'required|integer|min:1|max:' . Role::count()
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user_role = UserRole::where('user_id', Auth::user()->id)->first();

        if ($user_role->is_admin == 0 && $request->role_id == $this->admin_role_id) {
            return $this->apiResponse->sendResponse(400, 'User is not admin.', null);
        }

        try {
            if (!is_null(Auth::user()->chats()->where('chat_id', $request->chat_id)->first())) {
                $messages = ChatMessage::with(['sender' => function ($query) {
                    $query->select('id', 'name', 'avatar');
                }])->where('chat_id', $request->chat_id)->orderByDesc('created_at')->paginate($this->num_entries_per_page);
                return $this->apiResponse->sendResponse(200, 'Success', $messages);
            }
            return $this->apiResponse->sendResponse(400, 'Not Authorised', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function create_group_chat(request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user_role = UserRole::where('user_id', Auth::user()->id)->first();

        if ($user_role->is_admin == 0) {
            return $this->apiResponse->sendResponse(400, 'User is not a admin.', null);
        }

        try {
            $chat = new Chat();
            $chat->creator_id = Auth::user()->id;
            $chat->is_group = true;

            if (!isset($request->opportunity_id)) {
                $chat->title = $request->title;
                $chat->save();
            } else {
                // check if chat group exist
                if (!ChatGroup::where('opportunity_id', $request->opportunity_id)->exists()) {
                    // Create New Chat Group if it does not exist
                    $opportunity = Opportunity::find($request->opportunity_id);

                    $chat->title = $opportunity->title;
                    $chat->opportunity()->associate($opportunity);
                    $chat->save();

                    $chat->group()->create([
                        'opportunity_id' => $request->opportunity_id,
                    ]);

                    $chat->users()->attach([Auth::user()->id => ['role_id' => $this->admin_role_id]]);

                    // Add Chats representatives if needed
                    if ($request->add_representatives == true) {
                        $represntatives = OpportunityRepresentative::where('opportunity_id', $request->opportunity_id)->get();
                        foreach ($represntatives as $represntative) {
                            $chat->users()->attach([$represntative->representative_id => ['role_id' => $this->organization_role_id]]);
                        }
                    }
                } else {
                    $chat = Chat::where('id', ChatGroup::where('opportunity_id', $request->opportunity_id)->select('chat_id')->first()["chat_id"])->first();
                }
            }

            return $this->apiResponse->sendResponse(200, 'Chat created', $chat);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getLine());
        }
    }

    public function create_opportunity_chat(request $request)
    {
        $validator = Validator::make($request->all(), [
            'opportunity_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $chat = Auth::user()->chats()->with('opportunity')->whereHas('opportunity', function ($query) use ($request) {
                $query->where('id', $request->opportunity_id);
            })->first();

            if (is_null($chat)) {
                $opportunity = Opportunity::find($request->opportunity_id);

                $chat = new Chat();
                $chat->creator_id = Auth::user()->id;
                $chat->title = $opportunity->title;
                $chat->opportunity()->associate($opportunity);
                $chat->save();


                $chat->users()->attach([Auth::user()->id => ['role_id' => $this->user_role_id]]);
            }


            return $this->apiResponse->sendResponse(200, 'Success', $chat);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function create_support_chat()
    {

        try {
            $chat = Auth::user()->chats()->where('is_support', true)->first();
            if (is_null($chat)) {
                $chat = new Chat();
                $chat->title = "Precisely Support";
                $chat->creator_id = Auth::user()->id;
                $chat->is_support = true;
                $chat->save();

                $chat->users()->attach([Auth::user()->id => ['role_id' => $this->user_role_id]]);
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
            'role_id' => 'required|integer|min:1|max:' . Role::count()
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user_role = UserRole::where('user_id', Auth::user()->id)->first();
        if ($user_role->is_admin == 0) {
            return $this->apiResponse->sendResponse(400, 'User is not a admin.', null);
        }

        try {
            $chat = Chat::where('id', $request->chat_id)->first();
            if (is_null($chat)) {
                return $this->apiResponse->sendResponse(400, 'Chat does not exist.', null);
            }

            $chat->users()->attach([$request->user_id => ['role_id' => $request->role_id]]);

            return $this->apiResponse->sendResponse(200, 'User added to chat', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function add_chat_operator(request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'chat_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user_role = UserRole::where('user_id', Auth::user()->id)->first();
        if ($user_role->is_admin == 0) {
            return $this->apiResponse->sendResponse(400, 'User is not a admin.', null);
        }

        try {
            $chat = Chat::where('id', $request->chat_id)->first();
            if ($chat) {
                $chat_operator = new ChatOperator();
                $chat_operator->chat_id = $request->chat_id;
                $chat_operator->operator_id = $request->user_id;
                $chat_operator->save();

                return $this->apiResponse->sendResponse(200, 'Operator added to the chat.', null);
            } else {
                return $this->apiResponse->sendResponse(400, 'Chat does not exist.', null);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function send_message(request $request)
    {
        // TODO: Integrate fcm
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
            'message' => 'required|string',
            'role_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        try {
            $chat = Chat::where('id', $request->chat_id)->first();
            if (is_null($chat)) {
                return $this->apiResponse->sendResponse(400, 'Chat does not exist.', null);
            }

            $chat_message = new ChatMessage();
            $chat_message->message = $request->message;
            $chat_message->chat_id = $request->chat_id;
            $chat_message->role_id = $request->role_id;
            $chat_message->type_id = 1;
            $chat_message->sender_id = Auth::user()->id;
            $chat_message->save();

            // Send Notifications via get firebaseIDs
            $this->get_firebaseIds($request->chat_id, $chat_message->id, Auth::user()->id, Auth::user()->name);

            $chat_message = ChatMessage::with(['sender' => function ($query) {
                $query->select('id', 'name', 'avatar');
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
            'role_id' => 'required|integer|min:1|max:' . Role::count(),
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        try {
            $chat = Chat::where('id', $request->chat_id)->first();
            if (is_null($chat)) {
                return $this->apiResponse->sendResponse(400, 'Chat does not exist.', null);
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
            $chat_message->role_id = $request->role_id;
            $chat_message->type_id = $request->type_id;
            $chat_message->sender_id = Auth::user()->id;
            $chat_message->save();

            // Send Notifications via get firebaseIDs
            $this->get_firebaseIds($request->chat_id, $chat_message->id, Auth::user()->id, Auth::user()->name);

            return $this->apiResponse->sendResponse(200, 'Multimedia message Added', $chat_message);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function get_firebaseIds($chat_id, $message_id, $user_id, $sender_name)
    {
        $studentIds = StudentFirebase::with("user")->whereHas("user", function($query) use ($user_id, $chat_id) {
            $query->whereIn('id', Chat::find($chat_id))->where("user_id", '!=', $user_id);
        })->get()->pluck('firebaseId');

        // $studentIds = StudentFirebase::whereIn('user_id', ChatUser::where('chat_id', $chat_id)->where('user_id', '!=', $user_id)->get()->pluck('user_id'))->pluck('firebaseId');
        $adminIds = AdminFirebase::all()->pluck('firebaseId');

        $student_headers = array(
            "key: AIzaSyDovLKo3djdRbs963vqKdbj-geRWyzMTrg",
            "Authorization: key=" . "AAAAOjqNmFY:APA91bFaHsWDfwZqlt2uYKo7Lufj_4ZfP9tNK57HSZHIOD8kW-Rca-GlDbTyDBAAG3LacvqxUmgPK3zIzxoL6r6wwKWx_I7WEsqvYpjvhiZaCoK8CZtgDdmi8Gwp-xXtSruDgt_qKpWI",
            "Content-Type: application/json"
        );
        $this->send_user_notification($message_id, $studentIds, $sender_name, $student_headers);

        $admin_firebase = array(
            "key: AIzaSyBwTH4gMhdWKZd5dlxYbvY3SIYMREOzGZY",
            "Authorization: key=" . "AAAAgvxGJqg:APA91bHQCC7Av_6k-DhytBf0-lhgbO_omK2nfbThcwz4C49VF1EK500EnrK1HmxGTRpixPBVIxojkRmoys2U1FV4KfmIhTn-hFURrYSS9BIRS_-Op6E3Y4k7IQ-qirLKqyS8iw7qyv6v",
            "Content-Type: application/json"
        );
        $this->send_user_notification($message_id, $adminIds, $sender_name, $admin_firebase);
    }

    public function send_user_notification($messageId, $firebaseIds, $sender_name, $headers)
    {
        try{

        // Send Notification to app
        $messageData = ChatMessage::with('chat')->where('id', $messageId)->first();
        $messageData['sender_name'] = $sender_name;

        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array(
            'registration_ids' => $firebaseIds,
            'notification' => array(
                "title" => 'New message on Precisely',
                "image" => 'https://lithics.in/apis/ic_notification.png'
            ),
            'data' => $messageData,
            'android' => array("priority" => "high"),
            "webpush" => array(
                "headers" => array(
                    "Urgency" => "high"
                )
            )
        );

        $fields = json_encode($fields, JSON_UNESCAPED_SLASHES);

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

        curl_exec ( $ch );

        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_student_firebase_id(request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'firebase_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $firebase = new StudentFirebase();
            $firebase->user_id = Auth::user()->id;
            $firebase->deviceId = $request->device_id;
            $firebase->firebaseId = $request->firebase_id;
            $firebase->save();

            return $this->apiResponse->sendResponse(200, 'Success.', $firebase);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function add_admin_firebase_id(request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'firebase_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $firebase = new AdminFirebase();
            $firebase->user_id = Auth::user()->id;
            $firebase->deviceId = $request->device_id;
            $firebase->firebaseId = $request->firebase_id;
            $firebase->save();

            return $this->apiResponse->sendResponse(200, 'Success.', $firebase);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }
}
