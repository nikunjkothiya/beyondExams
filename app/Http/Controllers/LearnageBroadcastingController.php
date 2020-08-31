<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\UserLive;
use App\Session;
use App\SessionType;
use App\SessionUser;
use App\User;
use Carbon\Carbon;
use Exception;

class LearnageBroadcastingController extends Controller
{
    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function get_broadcast_sessions(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $unrestricted_sessions = Session::where('session_type', 1)->where('live', 1)->where('restricted', 0);
            $restricted_sessions = Session::where('session_type', 1)->where('live', 1)->where('restricted', 1)->whereHas('user', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            })->union($unrestricted_sessions)->get();

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
            })->union($live_public_sessions)->union($live_private_sessions)->union($upcoming_public_sessions)->get();

            if (count($sessions) == 0)
                return $this->apiResponse->sendResponse(404, 'No Scheduled session.', null);

            return $this->apiResponse->sendResponse(200, 'Success', $sessions);
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
                'email' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing or invalid.', $validator->errors());
            }

            $user = User::where('email', $request->email)->first();

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
