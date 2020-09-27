<?php

namespace App\Http\Middleware;

use App\UserLastLogin;
use Carbon\Carbon;
use Closure;
use Auth;

class UpdateLastLoginStatus
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
        $user_id = Auth::id();
        $loginActivity = UserLastLogin::where('user_id', $user_id)->first();
        if (is_null($loginActivity)) {
            $loginActivity = new UserLastLogin();
            $loginActivity->user_id = $user_id;
        }
        $loginActivity->updated_at = Carbon::now();
        $loginActivity->save();
        return $next($request);
    }
}
