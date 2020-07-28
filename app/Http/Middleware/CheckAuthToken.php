<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\ApiResponse;
use Auth;
use Illuminate\Support\Facades\DB;

class CheckAuthToken
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
        if (is_null($authorization)){
            return $apiResponse->sendResponse(401,'Missing Authorization',$authorization);
        }
        return $next($request);
    }
}
