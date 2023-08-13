<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Token;
use JwtApi;

class JwtMiddleware extends BaseMiddleware
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
        $token_obj = auth()->getToken() ? Token::findByValue(auth()->getToken()->get()) : null;

        if (!$token_obj) {
            return response()->json(['message' => 'Authorization Token not found'], 403);
        }

        // // IP Check
        // ! Can force IP generator of access_token
        // if ($token_obj->ip != JwtApi::getIp() ){
		// 	return response()->json(['status' => 'Token Invalid for this IP'], 403);
		// }

        // ! Can force device generator of access_token
        // if ($token_obj->device != JwtApi::getUserAgent() ){
		// 	return response()->json(['status' => 'Token Invalid for this device'], 403);
		// }

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => 'Token is Invalid'], 403);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => 'Token is Expired'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException) {
                return response()->json(['status' => 'Token is Blacklisted'], 400);
            } else {
                return response()->json(['status' => 'Authorization Token not found'], 404);
            }
        }
        return $next($request);
    }
}
