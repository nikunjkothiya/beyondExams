<?php

namespace App\Http\Controllers;

use App\Chat;
use App\ChatMessage;
use App\ChatReview;
use App\MessageType;
use Auth;
use Carbon\Carbon;
use DB;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\NewMessage;
use App\SaveMessage;
use App\StudentHomework;
use App\TimeTable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\TeacherDocument;

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
            //$user = User::find(2);
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
                /////Two way system binding message to 1 and 2 from chat_id (2 entries=>1 for sender,2 for receiver)
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

            //make event by php artisan make:event cmd and init these three value and brodcast event
            broadcast(new NewMessage($chat_message));

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
            //$ext = "." . $request->file('file')->getClientOriginalExtension();(both are same)
            $name = time() . uniqid() . $ext;

            $message_type = MessageType::find($request->type_id)["type"] . "/";
            $filePath = storage_path() . '/app/public/chats/' . $message_type;
            $file->move($filePath, $name);

            // dd(url('/storage/chats/' . $message_type . $name));
            $chat_message = new ChatMessage();
            $chat_message->message = url('/storage/chats/' . $message_type . $name);
            $chat_message->chat_id = $request->chat_id;
            $chat_message->type_id = $request->type_id;
            $chat_message->sender_id = Auth::user()->id;
            $chat_message->save();

            // Send Notifications via get firebaseIDs
            //make event by php artisan make:event cmd and init these three value and brodcast event
            //broadcast(new NewMessage($chat_message));

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

    public function add_time_table(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|int',
                'start_time' => 'required|string|date_format:H:i A',
                'end_time' => 'required|string|date_format:H:i A|after:start_time',
                'period_name' => 'required|string',
                'date' => 'required|string|date_format:d/m/Y',
                'day' => 'required|int|min:1|max:7',
            ]);


            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (!is_null(Chat::where('id', $request->chat_id)->first())) {

                $old_timetable = TimeTable::where(['chat_id' => $request->chat_id, 'start_time' => $request->start_time, 'end_time' => $request->end_time, 'period_name' => $request->period_name, 'date' => $request->date, 'day' => $request->day])->count();

                if ($old_timetable > 0) {
                    return $this->apiResponse->sendResponse(201, 'Already Exits.', null);
                } else {
                    $timetable = new TimeTable();
                    $timetable->teacher_id = Auth::user()->id;
                    $timetable->chat_id = $request->chat_id;
                    $timetable->start_time = $request->start_time;
                    $timetable->end_time = $request->end_time;
                    $timetable->period_name = $request->period_name;
                    $timetable->date = $request->date;
                    $timetable->day = $request->day;
                    $timetable->save();
                }
            } else {
                return $this->apiResponse->sendResponse(201, 'Chat Not Found.', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Timetable Created Successfully.', null);
        } catch (\Exception $e) {
            DB::rollback();
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function add_teacher_document(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'timetable_id' => 'required|int',
                'document_name' => 'required|file',
                'type' => 'required|int|min:1|max:3',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            $entryFound = TimeTable::where('id', $request->timetable_id)->count();

            if ($entryFound == 0) {
                return $this->apiResponse->sendResponse(201, 'Timetable Not Found.', null);
            } else {
                $attachment = $request->file('document_name');
                $original_name = $request->file('document_name')->getClientOriginalName();
                $storage_path = '/chats/teacher_document/';
                $imgpath = commonUploadImage($storage_path, $attachment);

                $teacherDocument = new TeacherDocument();
                $teacherDocument->timetable_id = $request->timetable_id;
                $teacherDocument->creator_id = Auth::user()->id;
                $teacherDocument->document_name = $original_name;
                $teacherDocument->document_path = $imgpath;
                $teacherDocument->type = $request->type;
                $teacherDocument->save();
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Document Store Successfully.', null);
        } catch (\Exception $e) {
            DB::rollback();
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function add_chat_review(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'timetable_id' => 'required|int',
                'review_message' => 'sometimes|string',
                'rating' => 'required|int|min:1|max:5',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            $entryFound = TimeTable::where('id', $request->timetable_id)->first();

            if (!is_null($entryFound)) {
                $chatFound = Chat::where('id', $entryFound->chat_id)->first();
                if (is_null($chatFound)) {
                    return $this->apiResponse->sendResponse(201, 'Chat Not Found.', null);
                } else {
                    $addReview = new ChatReview();
                    $addReview->timetable_id = $request->timetable_id;
                    $addReview->student_id = Auth::user()->id;
                    $addReview->review_message = $request->review_message;
                    $addReview->rating = $request->rating;
                    $addReview->save();
                }
            } else {
                return $this->apiResponse->sendResponse(201, 'Timetable Not Found.', null);
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Review Added Successfully.', null);
        } catch (\Exception $e) {
            DB::rollback();
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function add_student_homework(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'timetable_id' => 'required|int',
                'document_name' => 'required|file',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            $entryFound = TimeTable::where('id', $request->timetable_id)->count();

            if ($entryFound == 0) {
                return $this->apiResponse->sendResponse(201, 'Timetable Not Found.', null);
            } else {
                $attachment = $request->file('document_name');
                $original_name = $request->file('document_name')->getClientOriginalName();
                $storage_path = '/chats/student_homework/';
                $imgpath = commonUploadImage($storage_path, $attachment);

                $studentHomweork = new StudentHomework();
                $studentHomweork->timetable_id = $request->timetable_id;
                $studentHomweork->student_id = Auth::user()->id;
                $studentHomweork->document_name = $original_name;
                $studentHomweork->document_path = $imgpath;
                $studentHomweork->save();
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Homework Store Successfully.', null);
        } catch (\Exception $e) {
            DB::rollback();
            throw new HttpException(500, $e->getMessage());
        }
    }

    public function search_filter_messages(request $request)
    {
        $validator = Validator::make($request->all(), [
            'timetable_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $timetable = TimeTable::where('id', $request->timetable_id)->first();
            if (is_null($timetable)) {
                return $this->apiResponse->sendResponse(201, 'Chat or Timetable Not Found', null);
            } else {
                $messages = ChatMessage::with(['sender' => function ($query) {
                    $query->select('id', 'name');
                }])->where(['chat_id' => $timetable->chat_id, 'sender_id' => $request->user_id])->orderByDesc('created_at')->paginate($this->num_entries_per_page);
            }
            return $this->apiResponse->sendResponse(200, 'Filtered Messages get Successfully', $messages);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function save_chat_message(request $request)
    {
        $validator = Validator::make($request->all(), [
            'chat_message_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $chat = ChatMessage::where('id', $request->chat_message_id)->first();
            $data = SaveMessage::where(['chat_message_id' => $request->chat_message_id, 'student_id' => Auth::user()->id])->first();
            if (is_null($chat)) {
                return $this->apiResponse->sendResponse(404, 'Chat Message does not exist.', null);
            } elseif (!is_null($data)) {
                return $this->apiResponse->sendResponse(201, 'Already Saved Chat Message Found.', $data);
            } else {
                $save_chat_message = new SaveMessage();
                $save_chat_message->student_id = Auth::user()->id;
                $save_chat_message->chat_message_id = $request->chat_message_id;
                $save_chat_message->save();
            }
            return $this->apiResponse->sendResponse(200, 'Message Saved Successfully', $save_chat_message);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, 'Internal Server Error', $e->getMessage());
        }
    }
}
