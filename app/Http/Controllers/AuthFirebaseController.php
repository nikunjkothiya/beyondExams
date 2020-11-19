<?php

namespace App\Http\Controllers;

use App\MentorDetail;
use App\MentorVerification;
use App\User;
use App\UserDetail;
use App\UserLastLogin;
use Auth;
use Carbon\Carbon;
use DB;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Validator;

class AuthFirebaseController extends Controller
{
    private $msg;
    private $apiResponse;
    private $json_data;
    private $apiConsumer;
    private $db;
    private $auth;
    private $student_role_id = 1;
    private $mentor_role_id = 2;
    private $admin_role_id = 3;
    private $org_role_id = 4;

    public function __construct(Application $app, ApiResponse $apiResponse)
    {
        $this->msg = "";
        $this->apiResponse = $apiResponse;
        $this->apiConsumer = new Client();
        $this->auth = $app->make('auth');
        $this->db = $app->make('db');
    }

    public function verifyAccessToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'access_token' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
            }

            $auth = app('firebase.auth');

            $flag = 0;

            $idTokenString = $request->access_token;

            try {
                $firebase_tokens = $auth->verifyIdToken($idTokenString);
                $firebase_user = $auth->getUser($firebase_tokens->getClaim('user_id'));
            } catch (Exception $e) {
                if (str_contains($e->getMessage(), "two dots")) {
                    $firebase_user = $auth->getUser($request->access_token);
                } else
                    return $this->apiResponse->sendResponse(401, "Couldn't retrieve user", null);
            }

            $new_user = User::where('unique_id', $firebase_user->uid)->first();

            if (!$new_user) {
                $new_user = new User();
                $new_user->role_id = 1;
            }

            if (!is_null($firebase_user->displayName))
                $new_user->name = $firebase_user->displayName;

            $new_user->email = $firebase_user->email;
            $new_user->unique_id = $firebase_user->uid;

            if ($firebase_user->photoUrl && strlen($firebase_user->photoUrl))
                $new_user->avatar = $firebase_user->photoUrl;

            $new_user->save();

            if (!$new_user->social_accounts)
                $new_user->social_accounts()->create(
                    ['provider_id' => $firebase_user->uid, 'provider' => "google"]
                );

//            // Save user generic details
//            $new_details = UserDetail::where('user_id', $new_user->id)->first();
//            $break_name = explode(" ", $new_user->name, 2);
//            if (is_null($new_details)) {
//                $new_details = new UserDetail();
//                $new_details->user_id = $new_user->id;
//            }
//            $new_details->name = $new_user->name;
//            $new_details->firstname = $break_name[0];
//            if (count($break_name) > 1)
//                $new_details->lastname = $break_name[1];
//
//            if (!is_null($new_user->email))
//                $new_details->email = $new_user->email;
//
//            if (!is_null($new_user->phone))
//                $new_details->phone = $new_user->phone;
//
//            $new_details->save();

            $loginActivity = UserLastLogin::where('user_id', $new_user->id)->first();

            if (is_null($loginActivity)) {
                $loginActivity = new UserLastLogin();
                $loginActivity->user_id = $new_user->id;
            }

            $loginActivity->updated_at = Carbon::now();
            $loginActivity->save();

            $response = $this->proxyLogin($firebase_user->uid, 'password', $flag);

            $data = json_decode($response->getContent(), true)["data"];

            $data["flag"] = $new_user->flag;
            $data["unique_id"] = $firebase_user->uid;
            $data["phoenix_user_id"] = $new_user->id;
            $data["email"] = $new_user->email;
            $data["name"] = $new_user->name;
            $data["role_id"] = $new_user->role_id;

            return $this->apiResponse->sendResponse(200, 'Login Successful', $data);

        } catch (InvalidArgumentException $e) { // If the token has the wrong format
            return $this->apiResponse->sendResponse(401, $e->getMessage(), $e->getTraceAsString());
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function proxyLogin($unique_id, $password, $flag)
    {
        try {
            $user = User::where('unique_id', $unique_id)->first();

            if (!is_null($user)) {
                return $this->proxy('password', $flag, [
                    'username' => $unique_id,
                    'password' => $password,
                ]);
            } else {

                $data = [
                    'access_token' => '',
                    'expires_in' => '',
                    'refresh_token' => '',
                ];

                return $this->apiResponse->sendResponse(401, 'The user credentials were incorrect.', $data);
            }
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function proxy($grantType, $flag, array $data = [])
    {
        //    	Get Laravel app config
        $config = app()->make('config');
        $data = array_merge($data, [
            'client_id' => env('PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSWORD_CLIENT_SECRET'),
            'grant_type' => $grantType
        ]);
        try {
            $response = $this->apiConsumer->post(sprintf('%s/oauth/token', $config->get('app.url')), [
                'form_params' => $data
            ]);

            $data_response = json_decode($response->getBody());

            $token_data = [
                'unique_id' => $data["username"],
                'new' => $flag,
                'access_token' => $data_response->access_token,
                'expires_in' => $data_response->expires_in,
                'refresh_token' => $data_response->refresh_token,
            ];
            return $this->apiResponse->sendResponse(200, 'Login Successful', $token_data);
        } catch (BadResponseException $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function refresh(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'unique_id' => 'required',
                'refresh_token' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, $validator->errors(), null);
            }

            $user = User::where('unique_id', $request->unique_id)->first();
            if (!$user) {
                return $this->apiResponse->sendResponse(404, 'User not found.', null);
            }

            $refreshToken = $request->get('refresh_token');
            $response = $this->proxyRefresh($refreshToken, $request->get('unique_id'), $user->flag);
            return $response;
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function proxyRefresh($refreshToken, $unique_id, $flag)
    {
        return $this->proxy('refresh_token', $flag, [
            'refresh_token' => $refreshToken,
            'username' => $unique_id
        ]);
    }

    public function logout()
    {
        try {
            $response = 1;
            $user_id = Auth::user()->id;
            $accessTokens = $this->token($user_id);
            foreach ($accessTokens as $accessToken) {
                $response = $response * $this->proxyLogout($accessToken->id);
            }
            if ($response) {
                return $this->apiResponse->sendResponse(200, 'Token successfully destroyed', $this->json_data);
            }
            $response_data["message"] = "Logout Error";
            return $this->apiResponse->sendResponse(500, 'Internal server error 3', $response_data);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function token($user_id)
    {
        try {
            $token = $this->db
                ->table('oauth_access_tokens')
                ->where('user_id', $user_id)
                ->where('revoked', 0)
                ->get(['id']);
            return $token;
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse($e->getCode(), $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function proxyLogout($accessToken)
    {
        try {
            $refreshToken = $this->db
                ->table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken)
                ->update([
                    'revoked' => true
                ]);
            if ($refreshToken) {
                if ($this->revoke($accessToken)) {
                    return 1;
                }
            }
            return 0;
        } catch (Exception $e) {
            return 0;
        }
    }


    public function getStudentFlag($new_user)
    {
        $check_lang = UserDetail::select('language_id')->where('user_id', $new_user->id)->first();

        $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $new_user->id)->first();
        // Flags for user
        if ($check_lang) {
            if (UserDetail::select('email')->where('user_id', $new_user->id)->first()->email) {
                if ($check_tag) {
                    // If Category is filled
                    return 0;
                } else {
                    // If Category is not filled
                    return 3;
                }
            } else {
                return 2;
            }
        } else {
            // No Language Selected
            return 1;
        }
    }

    public function getMentorFlag($new_user)
    {
        $check_detail = MentorDetail::select('email')->where('user_id', $new_user->id)->first();
        $verified = MentorVerification::where('user_id', $new_user->id)->first();

        if ($check_detail) {
            // Details Filled Now Check Verification
            if ($verified->is_verified == 0) {
                // Mentor Details filled but not verified
                return 2;
            } elseif ($verified->is_verified == 1) {
                // Mentor Verified
                return 0;
            } elseif ($verified->is_verified == 2) {
                // Mentor Verified
                return 3;
            }
        } else {
            // Details Not Filled ie New User
            return 1;
        }
    }

}
