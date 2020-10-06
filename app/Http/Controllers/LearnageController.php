<?php

namespace App\Http\Controllers;

use App\AccessType;
use App\ClassMentor;
use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\UserLive;
use App\Session;
use App\SessionType;
use App\SessionUser;
use App\TimeRecursionType;
use App\User;
use App\ClassModel;
use App\ClassStudent;
use Carbon\Carbon;
use Auth;
use Exception;

class LearnageController extends Controller
{
    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function get_my_classes(){
        try {
            $class_ids = ClassMentor::where('mentor_id', Auth::user()->id)->get()->pluck('class_id');
            $classes = ClassModel::whereIn('id', $class_ids)->get();
            return $this->apiResponse->sendResponse(200, 'Success', $classes);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }

    }

    public function get_class_students(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'class_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }
            $student_ids = ClassStudent::where('class_id', $request->class_id)->get()->pluck('student_id');
            $students = User::whereIn('id', $student_ids)->get();
            return $this->apiResponse->sendResponse(200, 'Success', $students);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }

    }

    public function add_students_to_class(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'class_id' => 'required|integer',
                'student_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }
            $class_student = new ClassStudent();
            $class_student->class_id = $request->class_id;
            $class_student->student_id = $request->student_id;
            $class_student->save();
            return $this->apiResponse->sendResponse(200, 'Success', $class_student);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }

    }

    public function create_class(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'grade_id' => 'integer',
                'subject_id' => 'integer',
                'duration' => 'integer',
                'time_recursion_id' => 'required|integer|min:0|max:' . TimeRecursionType::count(),
                'access_type_id' => 'required|integer|min:0|max:' . AccessType::count(),
                'max_students' => 'integer',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $class = new ClassModel();
            $class->title = $request->title;
            $class->time_recursion_id = $request->time_recursion_id;
            $class->access_type_id = $request->access_type_id;
            if (isset($request->grade_id))
                $class->grade_id = $request->grade_id;
            if (isset($request->subject_id))
                $class->subject_id = $request->subject_id;
            if (isset($request->duration))
                $class->duration = $request->duration;
            if (isset($request->max_students))
                $class->max_students = $request->max_students;
            $class->mentor()->create([
                'mentor_id' => Auth::user()->id
            ]);
            $class->save();
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_class_title(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $class = ClassModel::where('id', $request->id)->first();
            if (is_null($class))
                return $this->apiResponse->sendResponse(404, 'Class does not exist.', null);

            $class->title = $request->title;
            $class->save();
            return $this->apiResponse->sendResponse(200, 'Class title updated', $class);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_class_grade(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'grade_id' => 'required|integer',
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $class = ClassModel::where('id', $request->id)->first();
            if (is_null($class))
                return $this->apiResponse->sendResponse(404, 'Class does not exist.', null);

            $class->grade_id = $request->grade_id;
            $class->save();
            return $this->apiResponse->sendResponse(200, 'Class Grade updated', $class);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_class_subject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_id' => 'required|integer',
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $class = ClassModel::where('id', $request->id)->first();
            if (is_null($class))
                return $this->apiResponse->sendResponse(404, 'Class does not exist.', null);

            $class->subject_id = $request->subject_id;
            $class->save();
            return $this->apiResponse->sendResponse(200, 'Class Subject updated', $class);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_class_schedule(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $class = ClassModel::where('id', $request->id)->first();
            if (is_null($class))
                return $this->apiResponse->sendResponse(404, 'Class does not exist.', null);

            if(isset($request->class_start_date))
                $class->class_start_date = Carbon::parse($request->class_start_date);
            if(isset($request->class_start_time))
                $class->class_start_time = Carbon::parse($request->class_start_time);
            if(isset($request->duration))
                $class->duration = Carbon::parse($request->duration);
            if(isset($request->time_recursion_id))
                $class->time_recursion_id = Carbon::parse($request->time_recursion_id);
            $class->save();
            return $this->apiResponse->sendResponse(200, 'Class Schedule updated', $class);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_class_access(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'access_type_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $class = ClassModel::where('id', $request->id)->first();
            if (is_null($class))
                return $this->apiResponse->sendResponse(404, 'Class does not exist.', null);

            $class->access_type_id = Carbon::parse($request->access_type_id);
            $class->save();
            return $this->apiResponse->sendResponse(200, 'Class Access updated', $class);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function terminate_old_sessions()
    {
        try {
            $sessions = Session::where('live_time', '<', Carbon::now()->subHours(12))->get();
            return $this->apiResponse->sendResponse(200, 'Test', $sessions);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_broadcast_sessions(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $unrestricted_sessions = Session::where('session_type', 1)->where('live', 1)->where('restricted', 0);
            $restricted_sessions = Session::where('session_type', 1)->where('live', 1)->where('restricted', 1)->whereHas('user', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })->union($unrestricted_sessions)->with('host:id,name,avatar')->get();

            return $this->apiResponse->sendResponse(200, 'Test', $restricted_sessions);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_scheduled_sessions(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $live_public_sessions = Session::where('session_type', 2)->where('live', 1)->where('restricted', 0);
            $live_private_sessions = Session::where('session_type', 2)->where('live', 1)->where('restricted', 1)->whereHas('user', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });

            $upcoming_public_sessions = Session::where('session_type', 2)->where('live', 0)->where('live_time', '>', Carbon::now())->where('restricted', 0);
            $sessions = Session::where('session_type', 2)->where('live', 0)->where('live_time', '>', Carbon::now())->where('restricted', 1)->whereHas('user', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })->union($live_public_sessions)->union($live_private_sessions)->union($upcoming_public_sessions)->with('host:id,name,avatar')->get();

            if (count($sessions) == 0)
                return $this->apiResponse->sendResponse(200, 'No Scheduled session.', []);

            return $this->apiResponse->sendResponse(200, 'Success', $sessions);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function get_session_by_peer(Request $request)
    {
        try {
            if (!isset($request->peer_id)) {
                return $this->apiResponse->sendResponse(400, 'Need a Peer/Room id to find session', null);
            }

            $user_id = $request->user_id;
            $session = Session::where('peer_id', $request->peer_id)->with('host:id,name,avatar')->first();

            if (is_null($session)) {
                return $this->apiResponse->sendResponse(404, 'Session does not exist', null);
            }

            if ($session->restricted == 1) {
                $access = SessionUser::where('session_id', $session->id)->where('user_id', $user_id)->first();
                if (is_null($access)) {
                    return $this->apiResponse->sendResponse(402, 'You do not have access to view this session', []);
                }
            }

            return $this->apiResponse->sendResponse(200, 'Success', $session);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function add_session(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'host_id' => 'required|integer',
                'peer_id' => 'string',
                'user_limit' => 'integer',
                'live' => 'boolean',
                'restricted' => 'boolean',
                'session_type' => 'integer',
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }
            $session = new Session();
            $session->title = $request->title;
            $session->host_id = $request->host_id;
            // $session->host_id = Auth::user()->id;

            // Optional Params
            if (isset($request->peer_id))
                $session->peer_id = $request->peer_id;

            if (isset($request->user_limit))
                $session->user_limit = $request->user_limit;

            if (isset($request->live))
                $session->live = $request->live;

            if (isset($request->live_time))
                $session->live_time = Carbon::parse($request->live_time);

            if (isset($request->restricted) && $request->restricted == 1) {
                if (!isset($request->users)) {
                    return $this->apiResponse->sendResponse(400, 'No User specified for access to restricted session', null);
                }
                $session->restricted = $request->restricted;
            }

            if (isset($request->session_type)) {
                $session_type = SessionType::where('id', $request->session_type)->first();
                if (is_null($session_type)) {
                    return $this->apiResponse->sendResponse(404, 'There is no such session type', null);
                }
                $session->session_type = $request->session_type;
            }

            $session->save();

            if ($session->restricted == 1) {
                // Add host for access
                $session_user = new SessionUser();
                $session_user->session_id = $session->id;
                $session_user->user_id = $request->host_id;
                $session_user->save();
                // Add other user for access
                foreach ($request->users as $user) {
                    $session_user = new SessionUser();
                    $session_user->session_id = $session->id;
                    $session_user->user_id = $user;
                    $session_user->save();
                }
            }

            return $this->apiResponse->sendResponse(200, 'Success', $session);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function update_session(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }
            $session = Session::where('id', $request->id)->first();
            if (is_null($session))
                return $this->apiResponse->sendResponse(404, 'No session found with given id.', null);
            if (isset($request->peer_id))
                $session->peer_id = $request->peer_id;
            if (isset($request->title))
                $session->title = $request->title;
            if (isset($request->live))
                $session->live = $request->live;
            if (isset($request->live_time))
                $session->live_time = Carbon::parse($request->live_time);
            if (isset($request->user_limit))
                $session->user_limit = $request->user_limit;

            $session->save();
            return $this->apiResponse->sendResponse(200, 'Broadcasting details updated.', $session);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function verify_user(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'email',
                'id' => 'integer',
            ]);

            if (isset($request->email)) {
                $user = User::where('email', $request->email)->first();
            } elseif (isset($request->id)) {
                $user = User::where('id', $request->id)->first();
            } else {
                return $this->apiResponse->sendResponse(400, 'Need a id of email to find user', null);
            }

            if (is_null($user))
                return $this->apiResponse->sendResponse(404, 'User not found', null);

            $data = array(
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
            );
            return $this->apiResponse->sendResponse(200, 'User found', $data);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function migrate_user_live_to_session()
    {
        try {
            $live_users = UserLive::all();
            if (count($live_users) == 0)
                return $this->apiResponse->sendResponse(404, 'No user is broadcasting.', null);

            foreach ($live_users as $session) {
                $new_session = new Session();
                $new_session->title = $session->name;
                $new_session->host_id = $session->user_id;
                $new_session->peer_id = $session->peer_id;
                $new_session->live = $session->live;
                $new_session->live_time = Carbon::parse($session->created_at);
                $new_session->created_at = $session->created_at;
                $new_session->updated_at = $session->updated_at;
                $new_session->save();
            }

            return $this->apiResponse->sendResponse(200, 'Success', $live_users);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }
}