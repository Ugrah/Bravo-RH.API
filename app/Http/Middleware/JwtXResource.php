<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

use App\Models\Token;
use JwtApi;

class JwtXResource extends BaseMiddleware
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

        $token_obj = Token::findByValue(auth()->getToken()->get());

        if (!$token_obj) {
            return response()->json(['status' => 'Token revoked'], 403);
        }

        if ($token_obj->payload['xtype'] != 'resource') {
            return response()->json(['status' => 'Token Misused'], 406);
        }

        return $next($request);
    }
}
