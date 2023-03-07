<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Traits\{AutoResponderTrait, SendResponseTrait};

class UserHasProgram
{
    use AutoResponderTrait, SendResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    
    public function handle(Request $request, Closure $next)
    {
        /* Check user has active program */
        if( !$request->user()->getActiveProgram() )
            return $this->apiResponse('error', '404', "No program started yet.");
        return $next($request);
    }
}
