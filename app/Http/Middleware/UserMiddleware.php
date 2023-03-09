<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth::check())
        {
            $user_list = (['User']);
            $user_role = Auth::user()->getRoleNames()
                ->first();
                // Auth::user()->status == 1
            if (in_array($user_role, $user_list) && Auth::user()->status == 1 && Auth::user()->is_deleted == 0)
            {
                return $next($request);
            }
            else
            {
                Auth::logout();
                return redirect()->route('login')
                    ->with('status', 'Error')
                    ->with('message', Config::get('constants.ERROR.ACCOUNT_ISSUE'));
            }
            return $next($request);
        }
        else
        {
            Auth::logout();
            return redirect()->route('login')
                ->with('status', 'Error')
                ->with('message', Config::get('constants.ERROR.OOPS_ERROR'));
        }
    }
}
