<?php

namespace App\Http\Controllers;

use App\MentorDetail;
use App\MentorVerification;
use App\StudentDetail;
use App\User;
use App\UserDetail;
use App\UserRole;
use App\UserSocial;
use Auth;
use DB;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Validator;

class ApiAuthController extends Controller
{
    private $msg;
    private $apiResponse;
    private $json_data;
    private $apiConsumer;
    private $db;
    private $auth;
    private $user_role_id = 1;
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

    public function auth($provider)
    {
        if ($provider == 'google') {
            $config = [
                'client_id' => env('GOOGLE_API_ID'),
                'client_secret' => env('GOOGLE_API_SECRET'),
                'redirect' => env('GOOGLE_API_REDIRECT')
            ];
            $provider = Socialite::buildProvider(GoogleProvider::class, $config);
            return $provider->stateless()->redirect();
        } elseif ($provider == 'facebook') {
            return Socialite::driver($provider)->redirectUrl(env('FACEBOOK_REDIRECT'))->stateless()->redirect();
        } else {
            //
        }
    }

    public function login($provider)
    {
        try {
            $provider_obj = NULL;
            if ($provider == 'google') {
                $config = [
                    'client_id' => env('GOOGLE_API_ID'),
                    'client_secret' => env('GOOGLE_API_SECRET'),
                    'redirect' => env('GOOGLE_API_REDIRECT')
                ];

                $provider_obj = Socialite::buildProvider(GoogleProvider::class, $config);
            } else if ($provider == 'facebook') {
                $provider_obj = Socialite::driver($provider);
            }
            $user = $provider_obj->stateless()->user();

            //            $name_list = explode(" ", $user->user["name"]);
            //            $last_name = "";
            //
            //            if (count($name_list) > 1) {
            //                $last_name = join(" ", array_slice($name_list, 1, count($name_list)));
            //            }
            //
            //            $email = "";
            //            if (isset($user->email))
            //                $email = $user->email;

            $request = new Request();
            $request->replace(['access_token' => $user->token]);
            return $this->verifyAccessToken($request, $provider);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse($e->getCode(), 'Internal server error', $e);
        }
    }

    public function verifyAccessToken(Request $request, $provider)
    {
        try {
            $validator = Validator::make($request->all(), [
                'access_token' => 'required',
                // 'user_role' => 'required'
            ]);

            if (!isset($request->user_role))
                $request->user_role = 1;

            if ($validator->fails()) {
                return $this->apiResponse->sendResponse(400, 'Parameters missing.', $validator->errors());
            }


            $global_user_id = "";
            $email = "";
            $phoenix_user_id = 1;

            $flag = 0;
            // Provider instance. To extract user details
            $provider_obj = NULL;
            if ($provider == 'google') {
                $config = [
                    'client_id' => env('GOOGLE_API_ID'),
                    'client_secret' => env('GOOGLE_API_SECRET'),
                    'redirect' => env('GOOGLE_API_REDIRECT')
                ];
                $provider_obj = Socialite::buildProvider(GoogleProvider::class, $config);

                $user = $provider_obj->userFromToken($request->access_token);
            } else if ($provider == 'facebook') {
                $provider_obj = Socialite::driver($provider);
                $user = $provider_obj->userFromToken($request->access_token);
            } else if ($provider == 'phone') {
                $auth = app('firebase.auth');
                //                gxDLU13HUuQeSJXSjbqAnwh9vzz2
                $idTokenString = $request->access_token;

                try {
                    $user = $auth->verifyIdToken($idTokenString);
                    $user->id = $user->getClaim('sub');
                    $user->email = null;
                    $user->name = null;
                } catch (InvalidArgumentException $e) { // If the token has the wrong format
                    return $this->apiResponse->sendResponse(401, 'Couldnt parse token', null);
                } catch (InvalidToken $e) { // If the token is invalid (expired ...)
                    return $this->apiResponse->sendResponse(401, "Token is invalid", null);
                }
            }

            $email = $user->email;
            // Check account in own database
            $check_account = UserSocial::where('provider_id', $user->id)->first();
            if ($check_account) {
                $user_id = UserSocial::where('provider_id', $user->id)->select('user_id')->first()->user_id;
                $phoenix_user_id = $user_id;
                $global_user_id = $user->id;


                // Assign Role Entry if not existing
                $check_user_role = UserRole::where('user_id', $user_id)->first();
                if (!$check_user_role) {
                    $newRole = new UserRole();
                    $newRole->user_id = $user_id;
                    if ($request->user_role == $this->user_role_id) {
                        $newRole->is_user = 1;
                        $newRole->is_mentor = 0;
                    }
                    if ($request->user_role == $this->mentor_role_id) {
                        $newRole->is_user = 0;
                        $newRole->is_mentor = 1;
                    }
                    $newRole->save();
                }

                // Returning Flags
                if ($request->user_role == $this->user_role_id) {
                    // Update User Roles
                    $check_user_role = UserRole::where('user_id', $user_id)->first();
                    $check_user_role->is_user = 1;
                    $check_user_role->save();
                    // Return Flag for user
                    $check_lang = UserDetail::select('language_id')->where('user_id', $user_id)->first();
                    if ($check_lang) {
                        $check_detail = StudentDetail::where('user_id', $user_id)->first();
                    } else {
                        $check_detail = StudentDetail::where('user_id', $user_id)->first();
                    }

                    $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user_id)->first();
                    // Flags for user
                    if ($check_lang) {
                        if ($check_detail) {
                            if ($check_tag) {
                                // If Category is filled
                                $flag = 0;
                            } else {
                                // If Category is not filled
                                $flag = 3;
                            }
                        } else {
                            $flag = 2;
                        }
                    } else {
                        // No Language Selected
                        $flag = 1;
                    }
                } elseif ($request->user_role == $this->mentor_role_id) {
                    // Update Mentor Roles
                    $check_user_role = UserRole::where('user_id', $user_id)->first();
                    $check_user_role->is_mentor = 1;
                    $check_user_role->save();
                    // Flags for Mentor
                    $check_detail = MentorDetail::where('user_id', $user_id)->first();
                    $verified = MentorVerification::where('user_id', $user_id)->first();
                    if (!$verified) {
                        $newMentorVerification = new MentorVerification();
                        $newMentorVerification->user_id = $user_id;
                        $newMentorVerification->is_verified = 0;
                        $newMentorVerification->save();
                        $verified = MentorVerification::where('user_id', $user_id)->first();
                    }
                    if ($check_detail) {
                        // Details Filled Now Check Verification
                        if ($verified->is_verified == 0) {
                            // Mentor Details filled but not verified
                            $flag = 2;
                        } elseif ($verified->is_verified == 1) {
                            // Mentor Verified
                            $flag = 0;
                        } elseif ($verified->is_verified == 2) {
                            // Mentor Verified
                            $flag = 3;
                        }
                    } else {
                        // Details Not Filled ie New User
                        $flag = 1;
                    }
                }
            } else {
                // Create New User
                if ($provider == 'google') {
                    $flag = 1;
                    $new_user = new User();
                    $new_user->name = $user->name;
                    $new_user->email = $user->email;
                    $new_user->unique_id = $user->id;
                    $new_user->avatar = $user->avatar;

                    $new_user->save();

                    $new_user->social_accounts()->create(
                        ['provider_id' => $user->id, 'provider' => $provider]
                    );

                    switch ($request->user_role) {
                        case $this->user_role_id:
                            $new_user->role()->create(
                                ['is_user' => 1, 'is_mentor' => 0]
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

                    $phoenix_user_id = $new_user->id;
                    $global_user_id = $user->id;
                    $email = $user->email;
                } elseif ($provider == 'facebook') {
                    $flag = 1;
                    $new_user = new User();
                    $new_user->name = $user->name;
                    $new_user->unique_id = $user->id;

                    if (isset($user->email))
                        $new_user->email = $user->email;
                    $new_user->avatar = $user->avatar;
                    $new_user->save();
                    $new_user->social_accounts()->create(
                        ['provider_id' => $user->id, 'provider' => $provider]
                    );

                    $phoenix_user_id = $new_user->id;
                    $global_user_id = $user->id;
                    $email = $user->email;
                } else if ($provider == 'phone') {
                    $auth = app('firebase.auth');
                    $firebase_user = $auth->getUser($user->id);
                    $claims = $firebase_user->getClaims();

                    $new_user = new User();
                    $new_user->unique_id = $user->id;
                    if (array_key_exists('phone_number', $claims))
                        $new_user->phone = $firebase_user->getClaim('phone_number');
                    else
                        $new_user->phone = null;

                    $new_user->save();
                    $new_user->social_accounts()->create(
                        ['provider_id' => $user->id, 'provider' => $provider]
                    );

                    switch ($request->user_role) {
                        case $this->user_role_id:
                            $new_user->role()->create(
                                ['is_user' => 1, 'is_mentor' => 0]
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

                    $phoenix_user_id = $new_user->id;
                    $global_user_id = $user->id;
                    $email = null;
                } else {
                    return $this->apiResponse->sendResponse(500, 'Provider not supported}', null);
                }

                // Save user generic details
                $new_details = UserDetail::where('user_id', $new_user->id)->first();
                $break_name = explode(" ", $new_user->name, 2);
                if (is_null($new_details)) {
                    $new_details = new UserDetail();
                    $new_details->user_id = $new_user->id;
                }
                $new_details->firstname = $break_name[0];
                if (count($break_name) > 1)
                    $new_details->lastname = $break_name[1];
                if (!is_null($new_user->email))
                    $new_details->email = $new_user->email;
                if (!is_null($new_user->phone))
                    $new_details->phone = $new_user->phone;
                $new_details->save();
            }

            $client = new Client();

            $res = $client->request('POST', 'https://lithics.in/apis/mauka/signup.php', [
                'form_params' => [
                    'user_id' => $phoenix_user_id,
                    'user_name' => "Precisely",
                    'source' => $provider
                ]
            ]);

            $result = $res->getBody()->getContents();
            DB::table('legacy_users')->insertOrIgnore(array('phoenix_user_id' => $phoenix_user_id, 'legacy_user_id' => $result));
            $data["legacy_user_id"] = $result;

            $response = $this->proxyLogin($global_user_id, 'password', $flag);
            $data = json_decode($response->getContent(), true)["data"];
            $data["phoenix_user_id"] = $phoenix_user_id;
            $data["email"] = $email;
            $data["user_name"] = $user->name;
            return $this->apiResponse->sendResponse(200, 'Login Successful', $data);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse(500, $e->getMessage(), $e->getTraceAsString());
        }
    }

    public function proxyLogin($unique_id, $password, $flag)
    {
        $user = User::where('unique_id', $unique_id)->first();


        //    	dd([$unique_id, $user]);

        if (!is_null($user)) {
            return $this->proxy('password', $flag, [
                'username' => $unique_id,
                'password' => $password,
            ]);
        } else {

            $data = [
                'unique_id' => $unique_id,
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

            if (!isset($request->user_role))
                $request->user_role = 1;

            if (!User::where('unique_id', $request->unique_id)->first()) {
                return $this->apiResponse->sendResponse(404, 'User not found.', null);
            }

            $user_id = User::select('id')->where('unique_id', $request->unique_id)->first()->id;

            if ($request->user_role == $this->user_role_id) {
                $check_lang = UserDetail::select('language_id')->where('user_id', $user_id)->first();
                if ($check_lang) {
                    $check_detail = UserDetail::where('user_id', $user_id)->first()->email;
                } else {
                    $check_detail = UserDetail::where('user_id', $user_id)->first();
                }

                $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user_id)->first();
                // Flags for user
                if ($check_lang) {
                    if ($check_detail) {
                        if ($check_tag) {
                            // If Category is filled
                            $flag = 0;
                        } else {
                            // If Category is not filled
                            $flag = 3;
                        }
                    } else {
                        $flag = 2;
                    }
                } else {
                    // No Language Selected
                    $flag = 1;
                }
            } elseif ($request->user_role == $this->mentor_role_id) {
                // Update Mentor Roles
                $check_user_role = UserRole::where('user_id', $user_id)->first();
                //                TODO: User shouldn't be allowed to simply modify one param and achieve mentor status
                $check_user_role->is_mentor = 1;
                $check_user_role->save();
                // Flags for Mentor
                $check_detail = MentorDetail::where('user_id', $user_id)->first();
                $verified = MentorVerification::where('user_id', $user_id)->first();
                if (!$verified) {
                    $newMentorVerification = new MentorVerification();
                    $newMentorVerification->user_id = $user_id;
                    $newMentorVerification->is_verified = 0;
                    $newMentorVerification->save();
                    $verified = MentorVerification::where('user_id', $user_id)->first();
                }
                if ($check_detail) {
                    // Details Filled Now Check Verification
                    if ($verified->is_verified == 0) {
                        // Mentor Details filled but not verified
                        $flag = 2;
                    } elseif ($verified->is_verified == 1) {
                        // Mentor Verified
                        $flag = 0;
                    } elseif ($verified->is_verified == 2) {
                        // Mentor Verified
                        $flag = 3;
                    }
                } else {
                    // Details Not Filled ie New User
                    $flag = 1;
                }
            }

            $refreshToken = $request->get('refresh_token');
            $response = $this->proxyRefresh($refreshToken, $request->get('unique_id'), $flag);
            return $response;
        } catch (Exception $e) {
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

    public function logout(Request $request)
    {
        try {
            $response = 1;
            $user_id = $request->user()->id;
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

    public function revoke($accessToken)
    {
        try {
            $Token = $this->db
                ->table('oauth_access_tokens')
                ->where('id', $accessToken)
                ->update([
                    'revoked' => true
                ]);
            if ($Token) {
                return 1;
            }
            return 0;
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse($e->getCode(), $e->getMessage(), $e->getTraceAsString());
        }
    }
}
