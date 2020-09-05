<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Log;
class ApiResponse extends Controller
{
    public function sendResponse($code,$message,$data){
    	if($code == 200 || $code == 201){
			if ( isset($data['token'])){
				$cookie = Cookie::make('access_token', $data["token"]);
				echo "cookie";
				echo $cookie;
				echo "response";
				return response([
					'status' => 'success',
					'status_code' => $code,
					'message' => $message,
					'data' => $data
				],$code)->withCookie($cookie);
			}
    		return response([
    			'status' => 'success',
    			'status_code' => $code,
    			'message' => $message,
    			'data' => $data
    		],$code);
    	}
    	else{
			Log::error($message);
			Log::error(json_encode($data));
    		return response([
    			'status' => 'error',
    			'status_code' => $code,
    			'message' => $message,
    			'data' => $data
    		],$code);
    	}
    }
}