<?php

namespace App\Http\Controllers;

use App\Chat;
use App\ChatMessage;
use App\ChatMessageLabel;
use App\ChatReview;
use App\ChatUser;
use App\ClassroomChatMessage;
use App\MessageType;
use Auth;
use Carbon\Carbon;
use DB;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\NewMessage;
use App\PeriodInfo;
use App\SaveMessage;
use App\StudentHomework;
use App\TimeTable;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\TeacherDocument;
use App\StudentAttendance;
use App\TeacherAttendance;
use App\TimetableHistory;
use Illuminate\Support\Facades\Config;
use App\Label;

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
        DB::beginTransaction();
        try {
            $chats = Auth::user()->chats()->orderByDesc('updated_at')->paginate($this->num_entries_per_page);
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Success', $chats);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_chat_messages(request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (Auth::user()) {
                if (!is_null(Auth::user()->chats()->where('chat_id', $request->chat_id)->first())) {
                    $messages = ChatMessage::with(['sender' => function ($query) {
                        $query->select('id', 'name', 'avatar');
                    }])->where('chat_id', $request->chat_id)->orderByDesc('created_at')->paginate($this->num_entries_per_page);

                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Successfully Get Chat Messages', $messages);
                } else {
                    return $this->apiResponse->sendResponse(404, 'Chat Not Found', null);
                }
            }
            return $this->apiResponse->sendResponse(403, 'Access to the chat is forbidden', null);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function create_chat(request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'user_id' => 'integer',
            'title' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if ($request->exists('user_id')) {
                $user_id = $request->user_id;
            } else {
                $user_id = 1;
            }

            $chat = Auth::user()->chats()->where(['receiver_id' => $user_id, 'title' => $request->title])->first();
            if (is_null($chat)) {
                $chat = new Chat();
                $chat->creator_id = Auth::user()->id;
                $chat->title = $request->title;
                $chat->receiver_id = $user_id;
                $chat->save();

                $this->add_admin_message("Hey! How may I help you?", $chat->id, 3);

                $chat->users()->attach([Auth::user()->id, $user_id]);
                /////Two way system binding message to 1 and 2 from chat_id (2 entries=>1 for sender,2 for receiver)
                DB::commit();
                if ($request->exists('period_name')) {
                    return $chat;
                }
                return $this->apiResponse->sendResponse(200, 'Successfully Create Chat', $chat);
            }
            if ($request->exists('period_name')) {
                return $chat;
            }
            return $this->apiResponse->sendResponse(200, 'Already Created Chat', $chat);
        } catch (Exception $e) {
            DB::rollback();
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
        DB::beginTransaction();
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
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Success', $chat);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function is_supprot_to_chat_type_id()
    {
        DB::beginTransaction();
        try {
            $chat = Chat::where('is_support', true)->update(['chat_type_id' => 2]);
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Support Type Update Successfully', null);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_all_whatsapp_chats()
    {
        DB::beginTransaction();
        try {
            $chat = Chat::where('chat_type_id', 3)->get();
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Successfully Get Whatssapp Chats', $chat);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function save_chat_image(Request $request)
    {
        DB::beginTransaction();
        try {
                $validator = Validator::make($request->all(), [
                    'image' => 'required|mimes:jpeg,png,jpg|max:512',
                    'chat_id'=>'required|int'
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
                }
                
                $avatar = $request->file('image');
                $storage_path = 'chat/avatar/';
                $imgpath = commonUploadImage($storage_path, $avatar);

                $chat_avatar = Chat::find($request->chat_id);
                if(!$chat_avatar){
                    return $this->apiResponse->sendResponse(404, 'Chat Not Found', null);
                }
                $chat_avatar->avatar = env('BASE_URL') . $imgpath;
                $chat_avatar->save();
                
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Chat Avatar Set Successfully', null);
            
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_whattsapp_chat_messages(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|int',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }
            $messages = ChatMessage::with('user')->where('chat_id', $request->chat_id)->orderBy('created_at', 'desc')->paginate(15);
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Successfully Get Whatssapp Chat Messages', $messages);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_file_path_for_whattsapp(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|integer',
                'folder_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $chat = Chat::find($request->chat_id);
            if (!is_null($chat)) {
                $name = $request->folder_name;

                $old_message = ChatMessage::where(['chat_id' => $chat->id])->where('type_id', '!=', 1)->get();
                foreach ($old_message as $message) {
                    //                    $change_path = ChatMessage::where(['chat_id' => $chat->id,'id'=>$message->id])->where('type_id','!=',1)->first();
                    $message->message = str_replace("WhatsApp-Scraping/", "classroom_assets/" . $name . "/", $message->message);
                    $message->message = env('BASE_URL') . $message->message;
                    $message->save();
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Successfully Changed Filepaths', null);
            } else {
                DB::commit();
                return $this->apiResponse->sendResponse(404, 'Chat Not Found', null);
            }
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function load_whatsapp_chat_into_db(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'chat_name' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $file = public_path('classroom_assets/' . $request->chat_name);

            $chat = new Chat();
            $chat->title = $request->chat_name;
            $chat->creator_id = Auth::user()->id;
            $chat->receiver_id = 1;
            $chat->chat_type_id = 3; // whattsapp
            $chat->save();

            $path = $file . '/chat.csv';
            $data = [];

            $lines = file($path);
            foreach ($lines as $key => $line) {
                $values = str_getcsv($line, '|');
                array_push($data, $values);
            }
            //$data = array_map('str_getcsv', file($path));

            if (!empty($data)) {
                foreach ($data as $key => $value) {
                    if ($key != 0) {
                        if (!empty($value)) {
                            $name = $value[1];
                            $findUser = User::where('name', '=', $name)->first();
                            if (is_null($findUser)) {
                                $newUser = new User();
                                $newUser->name = $name;
                                $newUser->unique_id = uniqid();
                                $newUser->flag = 0;
                                if (str_contains($value[1], '( Student)')) {
                                    $newUser->role_id = 1;
                                } else {
                                    $newUser->role_id = 2;
                                }
                                $newUser->language_id = 3;

                                $newUser->created_at = date('Y-m-d H:i:s', strtotime($value[0]));
                                $newUser->updated_at = date('Y-m-d H:i:s', strtotime($value[0]));
                                $newUser->save();
                            }

                            $chat_message = new ChatMessage();
                            $chat_message->chat_id = $chat->id;
                            if ($value[3] == 'Text') {
                                $chat_message->message = $value[2];
                            } else {
                                $chat_message->message = str_replace("WhatsApp-Scraping/", "classroom_assets/" . $request->chat_name . "/", $value[2]);
                                $chat_message->message = env('BASE_URL') . $chat_message->message;
                            }

                            if ($value[3] == 'Text') {
                                $chat_message->type_id = 1;
                            } elseif ($value[3] == 'Photo') {
                                $chat_message->type_id = 2;
                            } elseif ($value[3] == 'Video') {
                                $chat_message->type_id = 3;
                            } elseif ($value[3] == 'Audio') {
                                $chat_message->type_id = 4;
                            } elseif ($value[3] == 'File') {
                                $chat_message->type_id = 5;
                            } else {
                                $chat_message->type_id = 1;
                            }

                            if (is_null($findUser)) {
                                $chat_message->sender_id = $newUser->id;
                                if (!ChatUser::where(['chat_id' => $chat->id, 'user_id' => $newUser->id])->first())
                                    $chat->users()->attach([$newUser->id]);
                            } else {
                                $chat_message->sender_id = $findUser->id;
                                if (!ChatUser::where(['chat_id' => $chat->id, 'user_id' => $findUser->id])->first())
                                    $chat->users()->attach([$findUser->id]);
                            }

                            $chat_message->created_at = date('Y-m-d H:i:s', strtotime($value[0]));
                            $chat_message->updated_at = date('Y-m-d H:i:s', strtotime($value[0]));
                            $chat_message->save();
                        }
                    }
                }
            }
            return $this->apiResponse->sendResponse(200, 'Whattsapp Chat Updated Successfully', null);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
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
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
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
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
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
            if (!Chat::find($request->chat_id)) {
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
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
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
        try {
            $validator = Validator::make($request->all(), [
                // 'chat_id' => 'sometimes|int',
                'start_time' => 'required|string|date_format:H:i',
                'end_time' => 'required|string|date_format:H:i|after:start_time',
                'period_name' => 'required|string',
                'date' => 'required|string|date_format:d/m/Y',
                'recursive' => 'required|int|min:1|max:4',
                //// subject column add and update api also
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }
            $request->request->add(['title' => $request->period_name]);
            $response = $this->create_chat($request);

            if (!is_null(Chat::where('id', $response->id)->first())) {

                $old_timetable = TimeTable::where(['chat_id' => $response->id, 'start_time' => $request->start_time, 'end_time' => $request->end_time, 'period_name' => $request->period_name, 'date' => $request->date])->get();

                if (count($old_timetable) > 0) {
                    return $this->apiResponse->sendResponse(201, 'Already Exits.', $old_timetable);
                } else {
                    $timetable = new TimeTable();
                    $timetable->teacher_id = Auth::user()->id;
                    $timetable->chat_id = $response->id;
                    $timetable->start_time = $request->start_time;
                    $timetable->end_time = $request->end_time;
                    $timetable->period_name = $request->period_name;
                    $timetable->date = $request->date;
                    $timetable->recursive = $request->recursive;
                    $timetable->save();
                }
            } else {
                return $this->apiResponse->sendResponse(404, 'Chat Not Found.', null);
            }

            return $this->apiResponse->sendResponse(200, 'Timetable Created Successfully.', $timetable);
        } catch (\Exception $e) {

            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_timetable(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'timetable_id' => 'required|integer',
                'start_time' => 'sometimes|string|date_format:H:i',
                'end_time' => 'sometimes|string|date_format:H:i',
                'period_name' => 'sometimes|string',
                'date' => 'sometimes|string|date_format:d/m/Y',
                'recursive' => 'sometimes|int|min:1|max:4',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::user()->role_id == 2) {
                $get_timetable = TimeTable::where(['id' => $request->timetable_id, 'teacher_id' => Auth::user()->id])->get();
                if (!$get_timetable) {
                    return $this->apiResponse->sendResponse(404, 'Timetable Not Found.', null);
                } else {
                    $get_timetable = TimeTable::find($request->timetable_id);
                    $get_timetable->fill($request->all())->save();
                    if ($request->exists('period_name')) {
                        $update_chat_name = Chat::where(['id' => $get_timetable->chat_id])->update(['title' => $request->period_name]);
                    }
                }
            } else {
                return $this->apiResponse->sendResponse(401, 'Only Teacher Can Update Timetable.', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Timetable Updated Successfully.', $get_timetable);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_time_table(Request $request)
    {
        DB::beginTransaction();
        try {
            // if (Auth::user()->role_id == 2) {
            $chats = ChatUser::where('user_id', Auth::user()->id)->distinct()->pluck('chat_id');

            $timetable = TimeTable::with(['teacher', 'classroom'])->whereIn('chat_id', $chats)->orderByRaw("date ASC, start_time ASC")->get();
            DB::commit();

            if (count($timetable) > 0) {
                return $this->apiResponse->sendResponse(200, 'Timetable Get Successfully.', $timetable);
            }
            return $this->apiResponse->sendResponse(404, 'Timetable Not Found.', null);
            /* } else {
                return $this->apiResponse->sendResponse(401, 'Only Teacher Can Get Timetable.', null);
            } */
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function delete_time_table(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'timetable_id' => 'required|int',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::user()->role_id == 2) {
                $timetable = TimeTable::where(['id' => $request->timetable_id, 'teacher_id' => Auth::user()->id])->get();
                if ($timetable) {
                    $timetable->delete();
                    DB::commit();
                    return $this->apiResponse->sendResponse(200, 'Timetable Deleted Successfully.', null);
                } else {
                    return $this->apiResponse->sendResponse(404, 'Timetable Not Found.', null);
                }
            } else {
                return $this->apiResponse->sendResponse(401, 'Only Teacher Can Delete Timetable.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_teacher_document(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|int',
                'document' => 'required|file',
                'type' => 'required|int|min:1|max:3',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (!Chat::find($request->chat_id)) {
                return $this->apiResponse->sendResponse(404, 'Chat Not Found.', null);
            } else {
                if (Auth::user()->role_id == 2) {
                    $attachment = $request->file('document');
                    $original_name = $request->file('document')->getClientOriginalName();
                    $storage_path = '/chats/teacher_document/';
                    $imgpath = commonUploadImage($storage_path, $attachment);

                    $teacherDocument = new TeacherDocument();
                    $teacherDocument->chat_id = $request->chat_id;
                    $teacherDocument->creator_id = Auth::user()->id;
                    $teacherDocument->document = $original_name;
                    $teacherDocument->document_path = $imgpath;
                    $teacherDocument->type = $request->type;
                    $teacherDocument->save();
                } else {
                    DB::commit();
                    return $this->apiResponse->sendResponse(401, 'Only Teacher Can Upload The Documents.', null);
                }
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Document Store Successfully.', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function submit_chat_review(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|int',
                'review_message' => 'sometimes|string',
                'rating' => 'required|int|min:1|max:5',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            $chatReviewFound = Chat::find($request->chat_id)->reviews()->where('student_id', Auth::user()->id)->first();
            if (!is_null($chatReviewFound)) {
                return $this->apiResponse->sendResponse(201, 'Already Found Chat Review For This Chat.', $chatReviewFound);
            } else {
                $addReview = new ChatReview();
                $addReview->chat_id = $request->chat_id;
                $addReview->student_id = Auth::user()->id;
                $addReview->review_message = $request->review_message;
                $addReview->rating = $request->rating;
                $addReview->save();
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Review Added Successfully.', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function submit_student_homework(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|int',
                'document_name' => 'required|file',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (!Chat::find($request->chat_id)) {
                return $this->apiResponse->sendResponse(404, 'Chat Not Found.', null);
            } else {
                $attachment = $request->file('document_name');
                $original_name = $request->file('document_name')->getClientOriginalName();
                $storage_path = '/chats/student_homework/';
                $imgpath = commonUploadImage($storage_path, $attachment);

                $studentHomweork = new StudentHomework();
                $studentHomweork->chat_id = $request->chat_id;
                $studentHomweork->student_id = Auth::user()->id;
                $studentHomweork->document_name = $original_name;
                $studentHomweork->document_path = $imgpath;
                $studentHomweork->save();
            }

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Homework Store Successfully.', null);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function search_filter_messages(request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (!Chat::find($request->chat_id)) {
                return $this->apiResponse->sendResponse(404, 'Chat Not Found', null);
            } else {
                $messages = ChatMessage::with(['sender' => function ($query) {
                    $query->select('id', 'name');
                }])->where(['chat_id' => $request->chat_id, 'sender_id' => $request->user_id])->orderByDesc('created_at')->paginate($this->num_entries_per_page);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Filtered Messages get Successfully', $messages);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function save_chat_message(request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'chat_message_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $data = SaveMessage::where(['chat_message_id' => $request->chat_message_id, 'student_id' => Auth::user()->id])->first();
            if (!ChatMessage::find($request->chat_message_id)) {
                return $this->apiResponse->sendResponse(404, 'Chat Message does not exist.', null);
            } elseif (!is_null($data)) {
                return $this->apiResponse->sendResponse(201, 'Already Saved Chat Message Found.', $data);
            } else {
                $save_chat_message = new SaveMessage();
                $save_chat_message->student_id = Auth::user()->id;
                $save_chat_message->chat_message_id = $request->chat_message_id;
                $save_chat_message->save();
            }
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Message Saved Successfully', $save_chat_message);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function classroom_chat_message(request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'chat_message_id' => 'required|integer',
            'timetable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            if (!TimeTable::find($request->timetable_id)) {
                return $this->apiResponse->sendResponse(404, 'Time Table does not exist.', null);
            } elseif (!ChatMessage::find($request->chat_message_id)) {
                return $this->apiResponse->sendResponse(404, 'Chat Message Not Found.', null);
            } else {
                $save_chat_message = new ClassroomChatMessage();
                $save_chat_message->timetable_id = $request->timetable_id;
                $save_chat_message->chat_message_id = $request->chat_message_id;
                $save_chat_message->save();
            }
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Message Saved Successfully', $save_chat_message);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_teacher_attendance(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|int',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::user()->role_id == 2) {
                if (!Chat::find($request->chat_id)) {
                    return $this->apiResponse->sendResponse(404, 'Chat Not Found.', null);
                } else {
                    $teacherAttendance = new TeacherAttendance();
                    $teacherAttendance->chat_id = $request->chat_id;
                    $teacherAttendance->teacher_id = Auth::user()->id;
                    $teacherAttendance->save();
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Attendance Created Successfully.', $teacherAttendance);
            } else {
                DB::commit();
                return $this->apiResponse->sendResponse(401, 'Only Teachers Take Attendance.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_student_attendance(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'chat_id' => 'required|int',
                'teacher_attendance_id' => 'required|int',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::user()->role_id == 1) {
                if (!Chat::find($request->chat_id)) {
                    return $this->apiResponse->sendResponse(404, 'Chat Not Found.', null);
                } else {
                    if (!TeacherAttendance::find($request->teacher_attendance_id)) {
                        DB::commit();
                        return $this->apiResponse->sendResponse(404, 'Attendance Not Initialed By Teacher.', null);
                    } else {
                        $studentAttendance = new StudentAttendance();
                        $studentAttendance->chat_id = $request->chat_id;
                        $studentAttendance->student_id = Auth::user()->id;
                        $studentAttendance->teacher_attendance_id = $request->teacher_attendance_id;
                        $studentAttendance->save();

                        DB::commit();
                        return $this->apiResponse->sendResponse(200, 'Attendance Stored Successfully.', null);
                    }
                }
            } else {
                DB::commit();
                return $this->apiResponse->sendResponse(401, 'Only Students Put Their Attendance.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function start_class(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'timetable_id' => 'required|int',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::user()->role_id == 2) {
                if (!TimeTable::find($request->timetable_id)) {
                    return $this->apiResponse->sendResponse(404, 'Timetable Not Found.', null);
                } else {
                    $dt = Carbon::now();
                    $teacherStartTime = new TimetableHistory();
                    $teacherStartTime->timetable_id = $request->timetable_id;
                    $teacherStartTime->teacher_id = Auth::user()->id;
                    $teacherStartTime->time = $dt->toTimeString();
                    $teacherStartTime->save();
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Start Class Time Saved Successfully.', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'Only Teachers Can Start Class.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function end_class(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'timetable_id' => 'required|int',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (Auth::user()->role_id == 2) {
                if (!TimeTable::find($request->timetable_id)) {
                    return $this->apiResponse->sendResponse(404, 'Timetable Not Found.', null);
                } else {
                    $dt = Carbon::now();
                    $teacherStartTime = new TimetableHistory();
                    $teacherStartTime->timetable_id = $request->timetable_id;
                    $teacherStartTime->teacher_id = Auth::user()->id;
                    $teacherStartTime->time = $dt->toTimeString();
                    $teacherStartTime->save();
                }
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'End Class Time Saved Successfully.', null);
            } else {
                return $this->apiResponse->sendResponse(401, 'Only Teachers Can End Class.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_chat_messages_in_one_hour(request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'chat_id'  => 'required|integer',
            'datetime' => 'required|date',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $end_time = date("Y-m-d H:i:s", strtotime($request->datetime . "+60 minutes"));

            $chat = ChatMessage::where('chat_id', $request->chat_id)->whereBetween('updated_at', [$request->datetime, $end_time])->get();
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Chat Get Successfully', $chat);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function set_period_info(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'timetable_id'      => 'required|int',
                'topic'             => 'required|string',
                'topic_description' => 'sometimes',
                'start_datetime'    => 'required|date'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            if (!TimeTable::find($request->timetable_id)) {
                return $this->apiResponse->sendResponse(404, 'Timetable Not Found.', null);
            } else {
                $set_period = new PeriodInfo();
                $set_period->timetable_id = $request->timetable_id;
                $set_period->topic = $request->topic;
                if (isset($request->topic_description))
                    $set_period->topic_description = $request->topic_description;
                $set_period->start_datetime = $request->start_datetime;
                $set_period->save();

                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Period Info Set Successfully.', null);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_period_info(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'start_datetime' => 'required|date',
                'timetable_id' => 'required|int'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            $period = PeriodInfo::where(['start_datetime' => $request->start_datetime, 'timetable_id' => $request->timetable_id])->get();
            if (count($period) == 0) {
                return $this->apiResponse->sendResponse(404, 'Period Info Not Found.', null);
            }
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Period Info Get Successfully.', $period);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_label_to_chat_message(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'label_name'        => 'required|string',
                'chat_message_id'   => 'required|int',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(200, 'Parameters missing or invalid.', $validator->errors());
            }

            $label = Label::where('name', $request->label_name)->first();
            if (!$label) {
                $label = new Label();
                $label->name = $request->label_name;
                $label->save();
            }
            if (!ChatMessage::find($request->chat_message_id)) {
                return $this->apiResponse->sendResponse(404, 'Chat Message Not Found.', null);
            }

            $add_label_to_message = new ChatMessageLabel();
            $add_label_to_message->user_id = Auth::user()->id;
            $add_label_to_message->chat_message_id = $request->chat_message_id;
            $add_label_to_message->label_id =  $label->id;
            $add_label_to_message->save();

            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Label Add Successfully.', $add_label_to_message);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_label_messages(request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'label_name'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $label = Label::where('name', $request->label_name)->first();
            if ($label) {
                $chat_message_ids = ChatMessageLabel::where(['user_id' => Auth::user()->id, 'label_id' => $label->id])->pluck('chat_message_id');
                $label_messages = ChatMessage::whereIn('id', $chat_message_ids)->get();
                DB::commit();
                return $this->apiResponse->sendResponse(200, 'Chat Label Messages Get Successfully', $label_messages);
            }

            return $this->apiResponse->sendResponse(404, 'Chat Label Not Found', null);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_user_labels(request $request)
    {
        DB::beginTransaction();

        try {
            $label_ids = ChatMessageLabel::where('user_id', Auth::user()->id)->distinct()->pluck('label_id');
            $labels = Label::whereIn('id', $label_ids)->get();
            DB::commit();
            return $this->apiResponse->sendResponse(200, 'Labels Get Successfully', $labels);
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function filter_chat_messages(Request $request)
    {
        DB::beginTransaction();
        $validator = Validator::make($request->all(), [
            'chat_id'     => 'required|int',
            'role_id'     => 'sometimes|array', // integer
            'labels'      => 'sometimes|array', // string
            'file_type'   => 'sometimes|array', // string
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
        }

        try {
            $chat_messages = ChatMessage::where('chat_id', $request->chat_id);

            if (!empty($request->role_id)) {
                $filter_by_role = DB::table('chat_messages')
                    ->join('users', 'chat_messages.sender_id', '=', 'users.id')
                    ->where('chat_messages.chat_id', $request->chat_id)
                    ->whereIn('users.role_id', $request->role_id)
                    ->select('chat_messages.id as id', 'chat_messages.chat_id as chat_id', 'chat_messages.message as message', 'chat_messages.type_id as type_id', 'chat_messages.sender_id as sender_id', 'chat_messages.created_at as created_at', 'chat_messages.updated_at as updated_at', 'chat_messages.is_child as is_child');
                $chat_messages = $chat_messages->union($filter_by_role);
            }

            if (!empty($request->labels)) {
                $labels = Label::whereIn('name', $request->labels)->distinct()->pluck('id');
                $chat_message_ids = ChatMessageLabel::whereIn('label_id', $request->labels)->pluck('chat_message_id');
                $filter_by_label = ChatMessage::where('chat_id', $request->chat_id)->whereIn('id', $chat_message_ids);
                $chat_messages = $chat_messages->union($filter_by_label);
            }

            if (!empty($request->file_type)) {
                $file_type = MessageType::whereIn('type', $request->file_type)->distinct()->pluck('id');
                $filter_by_type = ChatMessage::whereIn('type_id', $file_type)
                    ->where('chat_messages.chat_id', $request->chat_id);
                $chat_messages = $chat_messages->union($filter_by_type);
            }

            return $this->apiResponse->sendResponse(200, 'Messages Get Successfully', $chat_messages->paginate(10));
        } catch (Exception $e) {
            DB::rollback();
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}
