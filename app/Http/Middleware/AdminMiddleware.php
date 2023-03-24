<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class AdminMiddleware
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
    if( isUserStatusActive() && Auth::user()->hasRole('Administrator')  || Auth::user()->hasRole('1_Company'))
        return $next($request);
      Auth::logout();
      if($request->ajax())
         return response()->json(['error' => 'error', 'message' => 'Session timeout'], 302);
      return redirect()->route('login')->with('status', 'Error')
      ->with('message', config('constants.ERROR.ACCOUNT_ISSUE'));
   }
}
