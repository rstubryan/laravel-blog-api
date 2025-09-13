<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthenticated.',
                'content' => null,
                'errors' => []
            ], 401);
        });
        $exceptions->render(function (RouteNotFoundException $e, $request) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthenticated.',
                'content' => null,
                'errors' => []
            ], 401);
        });
    })->create();
