<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use Auth;

// Models
use App\Role;
use App\UserRole;
use App\Chat;
use App\ChatAdmin;
use App\ChatGroup;
use App\ChatMessage;
use App\ChatOperator;
use App\ChatUser;
use App\Opportunity;
use App\OpportunityRepresentative;
use App\OpportunityTranslations;

class ChatController extends Controller
{
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

            if ($request->role_id == 1) {
                // Requested Role is User
                if ($user_role->is_user == 1) {
                    $chats = Chat::with(['chat_users' => function ($query) {
                        $query->where('user_id', Auth::user()->id)->where('role_id', 1);
                    }])->paginate(15);
                    return $this->apiResponse->sendResponse(200, 'Success', $chats);
                } else {
                    return $this->apiResponse->sendResponse(400, 'User is not a student.', null);
                }
            } else if ($request->role_id == 2) {
                // Requested Role is Mentor
                if ($user_role->is_mentor == 1) {
                    $chats = Chat::with(['chat_users' => function ($query) {
                        $query->where('user_id', Auth::user()->id)->where('role_id', 2);
                    }])->paginate(15);
                    return $this->apiResponse->sendResponse(200, 'Success', $chats);
                } else {
                    return $this->apiResponse->sendResponse(400, 'User is not a mentor.', null);
                }
            } else if ($request->role_id == 3) {
                // Requested Role is Mentor
                if ($user_role->is_admin == 1) {
                    $chats = Chat::paginate(15);
                    return $this->apiResponse->sendResponse(200, 'Success', $chats);
                } else {
                    return $this->apiResponse->sendResponse(400, 'User is not a admin.', null);
                }
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
        if ($user_role->is_admin == 0) {
            return $this->apiResponse->sendResponse(400, 'User is not a admin.', null);
        }

        try {
            $chat_users = ChatUser::where('chat_id', $request->chat_id)->where('user_id', Auth::user()->id)->where('role_id', $request->role_id)->get();
            if (count($chat_users) > 0) {
                $messages = ChatMessage::paginate(15);
                return $this->apiResponse->sendResponse(200, 'Success', $messages);
            }
            return $this->apiResponse->sendResponse(400, 'Not Authorised', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function create_group_chat(request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'role_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user_role = UserRole::where('user_id', Auth::user()->id)->first();
        if ($user_role->is_admin == 0) {
            return $this->apiResponse->sendResponse(400, 'User is not a admin.', null);
        }

        try {

            if (!$request->opportunity_id) {
                $chat = new Chat();
                $chat->title = $request->title;
                $chat->creator_id = Auth::user()->id;
                $chat->is_support = false;
                $chat->is_group = true;
                $chat->save();

                $chat->users()->create([
                    'user_id' => Auth::user()->id,
                    'role_id' => $request->role_id
                ]);
                return $this->apiResponse->sendResponse(200, 'Chat created', $chat);
            } else {
                // check if chat group exist
                $chat_group = ChatGroup::where('opportunity_id', $request->opportunity_id)->first();
                if (!$chat_group) {
                    // Create New Chat Group if it does not exist

                    $opp = OpportunityTranslations::where('id', $request->opportunity_id)->where('locale','en')->first();
                    $chat = new Chat();
                    $chat->title = $opp->title;
                    $chat->creator_id = Auth::user()->id;
                    $chat->is_support = false;
                    $chat->is_group = true;
                    $chat->save();

                    $chat->group()->create([
                        'opportunity_id' => $request->opportunity_id,
                    ]);

                    $chat->users()->create([
                        'user_id' => Auth::user()->id,
                        'role_id' => $request->role_id
                    ]);
                }
                // Get the chat
                $chat = Chat::where('id', $chat_group->chat_id)->first();

                // Add Chats representatives if needed
                if ($request->add_representatives == true) {
                    $represntatives = OpportunityRepresentative::where('opportunity_id', $request->opportunity_id)->get();
                    foreach ($represntatives as $represntative) {
                        $chat->users()->create([
                            'user_id' => $represntative->representative_id,
                            'role_id' => 4,
                        ]);
                    }
                }

                // Return the chat
                return $this->apiResponse->sendResponse(200, 'Succes', $chat);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
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

            // check if chat group exist
            $chat_group = ChatGroup::where('opportunity_id', $request->opportunity_id)->first();
            
            if (!$chat_group) {
                // Create New Chat Group if it does not exist
                $opp = OpportunityTranslations::where('id', $request->opportunity_id)->where('locale','en')->first();
                $chat = new Chat();
                $chat->title = $opp->title;
                $chat->creator_id = Auth::user()->id;
                $chat->is_support = false;
                $chat->is_group = true;
                $chat->save();

                $chat->group()->create([
                    'opportunity_id' => $request->opportunity_id,
                ]);

                $chat->users()->create([
                    'user_id' => Auth::user()->id,
                    'role_id' => 1
                ]);
            }
            // Get the chat
            $chat = Chat::where('id', $chat_group->chat_id)->first();
            return $this->apiResponse->sendResponse(200, 'Success', $chat);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function create_support_chat()
    {

        try {
            $chat = new Chat();
            $chat->title = 'Precisely Support';
            $chat->creator_id = Auth::user()->id;
            $chat->is_support = true;
            $chat->is_group = false;
            $chat->save();

            $chat->users()->create([
                'user_id' => Auth::user()->id,
                'role_id' => 1
            ]);
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
            'role_id' => 'required|integer',
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
            if (!$chat) {
                return $this->apiResponse->sendResponse(400, 'Chat does not exist.', null);
            }
            $chat->user()->create([
                'user_id' => $request->user_id,
                'role_id' => $request->role_id
            ]);

            return $this->apiResponse->sendResponse(200, 'User added to chat', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function add_chat_admin(request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        $user_role = UserRole::where('user_id', Auth::user()->id)->first();
        if ($user_role->is_admin == 0) {
            return $this->apiResponse->sendResponse(400, 'User is not a admin.', null);
        }

        try {
            $chatAdmin = new ChatAdmin();
            $chatAdmin->user_id = $request->user_id;
            $chatAdmin->save();

            return $this->apiResponse->sendResponse(200, 'New Chat admin created.', null);
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
            if (!$chat) {
                return $this->apiResponse->sendResponse(400, 'Chat does not exist.', null);
            }
            $chat->messages()->create([
                'message' => $request->message,
                'type_id' => 1,
                'sender_id' => Auth::user()->id
            ]);

            return $this->apiResponse->sendResponse(200, 'Message Added', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }

    public function send_multimedia_message(request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
            'file' => 'required',
            'type_id' => 'required|integer',
            'role_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }
        try {
            $chat = Chat::where('id', $request->chat_id)->first();
            if (!$chat) {
                return $this->apiResponse->sendResponse(400, 'Chat does not exist.', null);
            }
            $chat->messages()->create([
                'message' => 'File Uploaded',
                'type_id' => $request->type_id,
                'sender_id' => Auth::user()->id
            ]);

            return $this->apiResponse->sendResponse(200, 'Multimedia message Added', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }
}
