<?php

declare(strict_types=1);

use App\Http\Middleware\EnforceExpectsJson;
use App\Http\Middleware\IdentifyRequest;
use App\Http\Middleware\SecureHeaders;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        health: '/',
        apiPrefix: 'api',
        then: function () {}
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->api(append: [
            EnforceExpectsJson::class,
            SecureHeaders::class,
            IdentifyRequest::class,
        ]);
        $middleware->alias([
            'throttle.auth' => \App\Http\Middleware\ThrottleAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            if (! ($request->is('api/*') || $request->expectsJson())) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas connecté.",
            ], 401);
        });

    })->create();
