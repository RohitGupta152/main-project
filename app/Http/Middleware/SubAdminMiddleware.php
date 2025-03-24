<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; 

class SubAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    // public function handle(Request $request, Closure $next)
    // {
    //     // 1 is sub-Admin
    //     if (Auth::check() && Auth::user()->user_type === 2) {
    //         return $next($request);
    //     }

    //     return response()->json(['message' => 'Unauthorized. Sub-admin access required.'], 403);
    // }


    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized. Token is missing.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Check if the authenticated user is a Sub-Admin (user_type = 2)
        if (Auth::user()->user_type !== 2) {
            return response()->json([
                'message' => 'Sub-Admin Unauthorized. Invalid or expired sub-admin token.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }


}
