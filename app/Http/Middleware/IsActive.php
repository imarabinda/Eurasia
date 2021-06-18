<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Auth;
use App\Exceptions\IsActiveException;

class IsActive
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

        if(Auth::check() && Auth::user()->is_active != 1 && !session()->get('invisible')){
            Auth::logout();
            throw new IsActiveException();
        }


        return $next($request);
    }
}
