<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\ApiResponse;
use App\User;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $apiResponse = new ApiResponse;
        $authorization = $request->header('Authorization');
        if (is_null($authorization))
            return $apiResponse->sendResponse(401,'Missing Authorization',$authorization);
        $id = User::where('api_token',$authorization)->first();
        if(!isset($id->id))
            return $apiResponse->sendResponse(401,'Unauthenticated','');
        $request->request->add(['user' => $id]);
        return $next($request);
    }
}

