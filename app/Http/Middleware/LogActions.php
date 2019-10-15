<?php

namespace App\Http\Middleware;

use App\Log;
use Closure;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\Auth;

class LogActions
{
    public function handle($request, Closure $next)
    {
        /*$reqs = '';
        foreach ($request->toArray() as $key=>$val){
            if($key == '_token')
                continue;
            if (is_array($key))
                $key=implode('___',$key);
            if (is_array($val))
                $val=implode('___',$val);
            $reqs.=$key.' : '.$val.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }*/
        $except=['search', 'show', 'showAdmin', 'admins', 'notification', 'index', 'drivers', 'workers'];
        $action = app('request')->route()->getAction();

        $controller = class_basename($action['controller']);

        list($controller, $action) = explode('@', $controller);
        if (!in_array($action,$except)) {
            Log::create([
                'user_id' => Auth::user()->id,
                'controller' => $controller,
                'action' => $action,
                'requests' => $request->toArray(),
                'client_ip' => $request->getClientIps()[0],
                'url' => $request->url()
            ]);
        }
        return $next($request);
    }
}
