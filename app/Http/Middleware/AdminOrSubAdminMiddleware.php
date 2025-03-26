<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class AdminOrSubAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized. Token is missing.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Allow access only if user is Admin (1) or Sub-Admin (2)
        if (!in_array(Auth::user()->user_type, [1, 2])) {
            return response()->json([
                'message' => 'Access Denied. You do not have permission to access this resource.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
