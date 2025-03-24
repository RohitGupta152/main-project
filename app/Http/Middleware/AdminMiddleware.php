<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; 

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     // 1 is Admin
    //     if (Auth::check() && Auth::user()->user_type === 1) {
    //         return $next($request);
    //     }

    //     // return response()->json(['message' => 'Unauthorized. Admin access required.'], 403);
    // }

    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized. Token is missing.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Check if the authenticated user is an Admin (user_type = 1)
        if (Auth::user()->user_type !== 1) {
            return response()->json([
                'message' => 'Admin Unauthorized. Invalid or expired admin token.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
