<?php

namespace App\Http\Controllers;

use Auth;
use GuzzleHttp\Exception\BadResponseException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiResponse;
use Laravel\Socialite\Two\GoogleProvider;
use Validator;
use App\User;
use App\UserSocial;
use App\UserDetail;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Carbon\Carbon;
use DB;

class ApiAuthController extends Controller
{
    private $msg;
    private $apiResponse;
    private $json_data;
    private $apiConsumer;
    private $db;
    private $auth;

    public function __construct(Application $app, ApiResponse $apiResponse)
    {
        $this->msg = "";
        $this->apiResponse = $apiResponse;
        $this->apiConsumer = new Client();
        $this->auth = $app->make('auth');
        $this->db = $app->make('db');
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
            return $this->apiResponse->sendResponse($e->getCode(), 'Internal server error 4', $e);
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
            return $this->apiResponse->sendResponse($e->getCode(), 'Internal server error 6', $e);
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
            return $this->apiResponse->sendResponse($e->getCode(), 'Internal server error 5', $e);
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

            if (!User::where('unique_id', $request->unique_id)->first()) {
                return $this->apiResponse->sendResponse(404, 'User not found.', null);
            }

            $user_id = User::select('id')->where('unique_id', $request->unique_id)->first()->id;
            $check_lang = UserDetail::select('language_id')->where('user_id', $user_id)->first();
            if ($check_lang) {
                $check_detail = UserDetail::select('email')->where('user_id', $user_id)->first()->email;
            } else {
                $check_detail = UserDetail::select('email')->where('user_id', $user_id)->first();
            }

            $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user_id)->first();

            $flag = 1;
            if ($check_lang) {
                if ($check_detail) {
                    if ($check_tag) {
                        $flag = 0;
                    } else {
                        $flag = 3;
                    }
                } else {
                    $flag = 2;
                }
            } else {
                $flag = 1;
            }

            $refreshToken = $request->get('refresh_token');
            $unique_id = $request->get('unique_id');
            $response = $this->proxyRefresh($refreshToken, $unique_id, $flag);
            return $response;
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse($e->getCode(), 'Internal server error 7', $e);
        }
    }

    public function proxyRefresh($refreshToken, $unique_id, $flag)
    {
        return $this->proxy('refresh_token', $flag, [
            'refresh_token' => $refreshToken,
            'username' => $unique_id
        ]);
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

            dd($e);
            return $this->apiResponse->sendResponse($e->getCode(), 'Internal Server Error 2', $e->getMessage());
        }
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

            $name_list = explode(" ", $user->user["name"]);
            $last_name = "";

            if (count($name_list) > 1) {
                $last_name = join(" ", array_slice($name_list, 1, count($name_list)));
            }

            $email = "";
            if (isset($user->email))
                $email = $user->email;

            $request = new Request();
            $request->replace(['access_token' => $user->token]);
            return $this->verifyAccessToken($request, $provider);
//			$data = array("token"=>$user->token, "first_name"=>$name_list[0], "last_name"=>$last_name, "email"=>$email, "avatar"=>$user->avatar);
//
//			return $this->apiResponse->sendResponse(200,'Success', $data);
//    		dd($user);
        } catch (Exception $e) {
            return $this->apiResponse->sendResponse($e->getCode(), 'Internal server error', $e);
        }
    }

    public function verifyAccessToken(Request $request, $provider)
    {
        try {
            $validator = Validator::make($request->all(), [
                'access_token' => 'required',
            ]);

            if($validator->fails()){
                return $this->apiResponse->sendResponse(400,'Parameters missing.',$validator->errors());
            }

            $global_user_id = "";
            $email = "";

            $flag = 0;
//	        Provider instance. To extract user details
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
            $user = $provider_obj->userFromToken($request->access_token);
            $email = $user->email;
//    		Check account in own database
            $check_account = UserSocial::where('provider_id', $user->id)->first();
            if ($check_account) {
                $user_id = UserSocial::where('provider_id', $user->id)->select('user_id')->first()->user_id;
                $global_user_id = $user->id;

                $check_lang = UserDetail::select('language_id')->where('user_id', $user_id)->first();
                if ($check_lang) {
                    $check_detail = UserDetail::select('email')->where('user_id', $user_id)->first()->email;
                } else {
                    $check_detail = UserDetail::select('email')->where('user_id', $user_id)->first();
                }

                $check_tag = DB::table('tag_user')->select('tag_id')->where('user_id', $user_id)->first();

                if ($check_lang) {
                    if ($check_detail) {
                        if ($check_tag) {
                            $flag = 0;
                        } else {
                            $flag = 3;
                        }
                    } else {
                        $flag = 2;
                    }
                } else {
                    $flag = 1;
                }

            } else {

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
                } elseif ($provider == 'facebook') {
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
                } else {
                    return $this->apiResponse->sendResponse(500, 'Internal server error 1', null);
                }

                $global_user_id = $user->id;
                $email = $user->email;
            }

            $phoenix_user = User::where('unique_id', $global_user_id)->first();
            $client = new Client();
            $res = $client->request('POST', 'https://lithics.in/apis/mauka/signup.php', [
                'form_params' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'source'=>$provider
                ]
            ]);

            $result = $res->getBody()->getContents();
            DB::table('legacy_users')->insertOrIgnore(array('phoenix_user_id'=>$phoenix_user->id, 'legacy_user_id'=>$result));

            $response = $this->proxyLogin($global_user_id, 'password', $flag);
	    $data = json_decode($response->getContent(), true)["data"];
	    $data["email"] = $email;
	    $data["legacy_user_id"] = $result;
	    $data["user_name"] = $user->name;
            return $this->apiResponse->sendResponse(200, 'Login Successful', $data);

        } catch (BadResponseException $e) {
            return $this->apiResponse->sendResponse($e->getCode(), 'Invalid Access Tokens', null);
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
}
