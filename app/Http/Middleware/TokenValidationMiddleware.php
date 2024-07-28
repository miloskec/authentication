<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                throw new AuthenticationException('User not found!');
            }
        } catch (JWTException $e) {
            // If token is invalid, expired or not present
            throw new AuthenticationException('Token is invalid: ' . $e->getMessage());
        }

        // Add user to request attributes if further user-specific access control is required
        $request->attributes->set('authenticated_user', $user);

        return $next($request);
    }
}
