<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\SendResponseTrait;
use App\Models\User;
class ValidateApiUser
{
    use SendResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if( !$request->user() )
            return $this->apiResponse('error', '404', "User not loged in.");
        // addUserNotification( $request->user() , "MESSAGE - " . time() , User::find(1) );
        return $next($request);
    }
}
