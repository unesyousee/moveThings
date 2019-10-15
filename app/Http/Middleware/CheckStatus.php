<?php

namespace App\Http\Middleware;

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        //If the status is not approved redirect to login
        if (Auth::check() && Auth::user()->status != 1) {
            Auth::logout();
            return redirect('/login')->with('erro_login', 'حساب کاربری شما غیر فعال است');
        }
        return $response;
    }
}
