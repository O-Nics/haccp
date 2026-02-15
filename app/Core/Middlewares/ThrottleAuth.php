<?php

declare(strict_types=1);

namespace App\Core\Middlewares;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ThrottleAuth
{
    public function __construct(
        private RateLimiter $limiter
    ) {}

    public function handle(Request $request, Closure $next, string $prefix = 'login', ?int $maxAttempts = null, ?int $decayMinutes = null): Response
    {
        if (app()->environment(['local', 'testing'])) {
            return $next($request);
        }

        if (! config('auth.security.rate_limit.enabled')) {
            return $next($request);
        }

        $key = $this->resolveRequestSignature($request, $prefix);
        $maxAttempts ??= config('auth.security.rate_limit.login_attempts');
        $decayMinutes ??= config('auth.security.rate_limit.lockout_minutes');

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $this->limiter->availableIn($key);

            return response()->json([
                'message' => sprintf(
                    'Trop de tentatives. Veuillez réessayer dans %d minute(s).',
                    (int) ceil($seconds / 60)
                ),
                'retry_after' => $seconds,
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        if ($response->isSuccessful()) {
            $this->limiter->clear($key);
        }

        return $response;
    }

    private function resolveRequestSignature(Request $request, string $prefix): string
    {
        return sprintf(
            'auth-%s:%s:%s',
            $prefix,
            sha1($request->ip()),
            sha1($request->input('email', ''))
        );
    }
}
