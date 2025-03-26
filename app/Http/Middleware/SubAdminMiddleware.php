<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SubAdminMiddleware
{
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
