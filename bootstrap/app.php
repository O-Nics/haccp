<?php

declare(strict_types=1);

use App\Core\Middlewares\EnforceExpectsJson;
use App\Core\Middlewares\IdentifyRequest;
use App\Core\Middlewares\SecureHeaders;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        health: '/',
        then: function () {}
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->api(append: [
            EnforceExpectsJson::class,
            SecureHeaders::class,
            IdentifyRequest::class,
        ]);
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if (! ($request->is('api/*') || $request->expectsJson())) {
                return null;
            }

            return response()->json([
                'success' => false,
                'message' => "Vous n'êtes pas connecté.",
            ], 401);
        });

        // 🔥 Post-traitement global des réponses d'erreur API
        $exceptions->respond(function (
            SymfonyResponse $response,
            Throwable $e,
            Request $request
        ) {

            if (! app()->isProduction()) {
                return $response;
            }
            // On ne touche que l'API / JSON
            if (! ($request->is('api/*') || $request->expectsJson())) {
                return $response;
            }

            // On ne modifie que les réponses JSON
            if (! $response instanceof JsonResponse) {
                return $response;
            }

            $data = $response->getData(true);

            $message = $data['message'] ?? null;

            if (! is_string($message) || $message === 'Server Error') {
                $message = 'Une erreur interne est survenue';
            }

            if (mb_strlen($message) > 45) {
                $message = 'Une erreur est survenue';
            }

            $data['message'] = $message;

            $response->setData($data);

            return $response;
        });

    })->create();
