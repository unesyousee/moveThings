<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class enabledUser
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
//        $status =Auth::user()->status;
//        if( $status != 1){
//            return response()->json(array(
//                "status" => 401,
//                "message" => 'حساب شما غیر فعال می‌باشد'
//            ));
//        }else{
//        }
//        return $next($request);
    }
}
