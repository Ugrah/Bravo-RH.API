<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;


class JwtXRefresh extends BaseMiddleware
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
        $payload = JWTAuth::payload();

        $error = !$payload->get('xtype') || $payload->get('xtype') != 'auth' || $request->get('xtype') != 'refresh';
        
        if ($error) {
            return response()->json(['status' => 'Token Misused'], 406);
        }

        return $next($request);
    }
}
