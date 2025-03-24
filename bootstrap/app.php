<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            // 'auth.user' => \App\Http\Middleware\AuthenticateUser::class,
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'auth.admin' => \App\Http\Middleware\AdminMiddleware::class,
            'auth.sub-admin' => \App\Http\Middleware\SubAdminMiddleware::class,
            'admin_or_sub-admin' => \App\Http\Middleware\AdminOrSubAdminMiddleware::class,
        ]);
        
    })

    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (AuthenticationException $exception, $request) {
            $authorizationHeader = $request->header('Authorization');

            if (!$authorizationHeader) {
                return response()->json([
                    'message' => 'User is Unauthorized.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            return response()->json([
                'message' => 'Token is Invalid.'
            ], Response::HTTP_UNAUTHORIZED);
        });


        // $exceptions->render(function (AuthenticationException $exception, $request) {
        //     $authorizationHeader = $request->header('Authorization');

        //     if (!$authorizationHeader) {
        //         return response()->json([
        //             'message' => 'Unauthorized. Token is missing.'
        //         ], Response::HTTP_UNAUTHORIZED);
        //     }

        //     // Custom messages for different roles
        //     if ($request->routeIs('auth.admin')) {
        //         return response()->json([
        //             'message' => 'Admin Unauthorized. Invalid or expired admin token.'
        //         ], Response::HTTP_UNAUTHORIZED);
        //     }

        //     if ($request->routeIs('auth.sub-admin')) {
        //         return response()->json([
        //             'message' => 'Sub-Admin Unauthorized. Invalid or expired sub-admin token.'
        //         ], Response::HTTP_UNAUTHORIZED);
        //     }

        //     return response()->json([
        //         'message' => 'Token is invalid or expired.'
        //     ], Response::HTTP_UNAUTHORIZED);
        // });

    })->create();
