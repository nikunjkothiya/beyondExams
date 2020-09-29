<?php

namespace App\Http\Controllers;

use App\MentorDetail;
use App\MentorVerification;
use App\StudentDetail;
use App\User;
use App\UserLastLogin;
use App\UserRole;
use App\UserSocial;
use Auth;
use DB;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Validator;
use DB;
use Carbon\Carbon;

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

            if (!isset($request->user_role))
                $request->user_role = 1;

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
            }

            $auth = app('firebase.auth');

            $flag = 0;

            $idTokenString = $request->access_token;

            $firebase_user = $auth->verifyIdToken($idTokenString);

            if ($firebase_user != null) {
                $provider = $firebase_user->getClaim('firebase')->sign_in_provider;
                $claims = $firebase_user->getClaims();
                $firebase_user->id = $firebase_user->getClaim('sub');

                if (array_key_exists('phone_number', $claims))
                    $firebase_user->phone = $firebase_user->getClaim('phone_number');
                else
                    $firebase_user->phone = null;

                if (array_key_exists('email', $claims))
                    $firebase_user->email = $firebase_user->getClaim('email');
                else
                    $firebase_user->email = null;
                if (array_key_exists('name', $claims))
                    $firebase_user->name = $firebase_user->getClaim('name');
                else
                    $firebase_user->name = null;
                if (array_key_exists('picture', $claims))
                    $firebase_user->avatar = $firebase_user->getClaim('picture');
                else
                    $firebase_user->avatar = null;
            } else {
                return $this->apiResponse->sendResponse(401, "Couldn't retrieve user", null);
            }

            $check_account = UserSocial::where('provider_id', $firebase_user->id)->first();
            $new_user = null;
            if ($check_account) {
                $new_user = User::find($check_account->user_id);
                $new_user->name = $firebase_user->name;
                $new_user->email = $firebase_user->email;
                $new_user->phone = $firebase_user->phone;
                $new_user->unique_id = $firebase_user->id;
                $new_user->avatar = $firebase_user->avatar;
                $new_user->save();

                // Assign Role Entry if not existing
                if (!$new_user->role()) {
                    $newRole = new UserRole();
                    $newRole->user_id = $new_user->id;
                    if ($request->user_role == $this->student_role_id)
                        $newRole->is_user = 1;
                    if ($request->user_role == $this->mentor_role_id)
                        $newRole->is_mentor = 1;

                    $newRole->save();
                } else {
                    if ($request->user_role == $this->student_role_id)
                        $new_user->role->is_user = 1;
                    if ($request->user_role == $this->mentor_role_id)
                        $new_user->role->is_mentor = 1;

                    $new_user->role->save();
                }

                if ($request->user_role == $this->student_role_id) {

                    $flag = $this->getStudentFlag($new_user);
                } else if ($request->user_role == $this->mentor_role_id) {
                    $verified = MentorVerification::where('user_id', $new_user->id)->first();
                    if (!$verified) {
                        $newMentorVerification = new MentorVerification();
                        $newMentorVerification->user_id = $new_user->id;
                        $newMentorVerification->is_verified = 0;
                        $newMentorVerification->save();
                    }
                    $flag = $this->getMentorFlag($new_user);
                }

            } else {
                $flag = 1;
                $new_user = new User();
                $new_user->name = $firebase_user->name;
                $new_user->email = $firebase_user->email;
                $new_user->phone = $firebase_user->phone;
                $new_user->unique_id = $firebase_user->id;
                $new_user->avatar = $firebase_user->avatar;

                $new_user->save();

                $new_user->social_accounts()->create(
                    ['provider_id' => $firebase_user->id, 'provider' => $provider]
                );

                switch ($request->user_role) {
                    case $this->student_role_id:
                        $new_user->role()->create(
                            ['is_user' => 1]
                        );
                        break;
                    case $this->mentor_role_id:
                        $new_user->role()->create(
                            ['is_mentor' => 1]
                        );
                        $new_user->mentor_verification()->create(
                            ['is_verified' => 0]
                        );
                        break;
                    case $this->admin_role_id:
                        $new_user->role()->create(
                            ['is_admin' => 1]
                        );
                        break;
                    case $this->org_role_id:
                        $new_user->role()->create(
                            ['is_organisation' => 1]
                        );
                        break;
                }
            }

            $loginActivity = UserLastLogin::where('user_id', $new_user->id)->first();
            if(is_null($loginActivity)){
                $loginActivity = new UserLastLogin();
                $loginActivity->user_id = $new_user->id;
            }
            $loginActivity->updated_at = Carbon::now();
            $loginActivity->save();

            $client = new Client();

            $res = $client->request('POST', 'https://lithics.in/apis/mauka/signup.php', [
                'form_params' => [
                    'user_id' => $new_user->id,
                    'user_name' => "Precisely",
                    'source' => "firebase"
                ]
            ]);

            $result = $res->getBody()->getContents();
            DB::table('legacy_users')->insertOrIgnore(array('phoenix_user_id' => $new_user->id, 'legacy_user_id' => $result));
            $data["legacy_user_id"] = $result;

            $response = $this->proxyLogin($firebase_user->id, 'password', $flag);
            $data = json_decode($response->getContent(), true)["data"];
            $data["unique_id"] = $firebase_user->id;
            $data["phoenix_user_id"] = $new_user->id;
            $data["email"] = $new_user->email;
            $data["user_name"] = $new_user->name;
            $data["phone"] = $new_user->phone;
            return $this->apiResponse->sendResponse(200, 'Login Successful', $data);

        } catch (\InvalidArgumentException $e) { // If the token has the wrong format
            return $this->apiResponse->sendResponse(401, $e->getMessage(), $e->getTraceAsString());
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function getStudentFlag($new_user)
    {
        $check_lang = User::select('language_id')->where('id', $new_user->id)->first();

        $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $new_user->id)->first();
        // Flags for user
        if ($check_lang) {
            if (StudentDetail::select('email')->where('user_id', $new_user->id)->first()->email) {
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

    public function proxyLogin($unique_id, $password, $flag)
    {
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
    }

    public function proxy($grantType, $flag, array $data = [])
    {
        //    	Get Laravel app config
        //$details = User::where('email',$data['username'])->first();
        $config = app()->make('config');
        $data = array_merge($data, [
            'client_id' => env('PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSWORD_CLIENT_SECRET'),
            'grant_type' => $grantType
        ]);
        try {
            //$user = User::where('email',$data['username'])->first();
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
            $response = json_decode($e->getResponse()->getBody());
            $data = [
                'access_token' => '',
                'expires_in' => '',
                'refresh_token' => '',
            ];

            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function refresh(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'unique_id' => 'required',
                'refresh_token' => 'required',
                'user_role' => 'integer'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, $validator->errors(), null);
            }

            $user = User::where('unique_id', $request->unique_id)->first();
            if (!$user) {
                return $this->apiResponse->sendResponse(404, 'User not found.', null);
            }

            $loginActivity = UserLastLogin::where('user_id', $user->id)->first();
            if(is_null($loginActivity)){
                $loginActivity = new UserLastLogin();
                $loginActivity->user_id = $user->id;
            }
            $loginActivity->updated_at = Carbon::now();
            $loginActivity->save();

            if ($request->user_role == $this->student_role_id) {
                $flag = $this->getStudentFlag($user);
            } elseif ($request->user_role == $this->mentor_role_id) {
                $flag = $this->getMentorFlag($user);
            }

            $refreshToken = $request->get('refresh_token');
            $response = $this->proxyRefresh($refreshToken, $request->get('unique_id'), $flag);
            return $response;
        } catch (\Exception $e) {
            return $this->apiResponse->sendResponse($e->getCode(), $e->getMessage(), $e->getTraceAsString());
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
            return $this->apiResponse->sendResponse($e->getCode(), $e->getMessage(), $e->getTraceAsString());
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
}
