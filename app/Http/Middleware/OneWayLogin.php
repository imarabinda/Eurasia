<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\OneWayLoginException;

class OneWayLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::check() && !empty(Auth::user()->last_session) && session()->getId() !== Auth::user()->last_session && !session()->get('invisible')){
            Auth::logout();
            throw new OneWayLoginException("Another device logged in. You'll be logged out.");
        }
        return $next($request);
    }
}
