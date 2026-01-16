<?php

// app/Http/Middleware/ThrottleAuth.php

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

    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment(['local', 'testing'])) {
            return $next($request);
        }

        if (! config('auth.security.rate_limit.enabled')) {
            return $next($request);
        }

        $key = $this->resolveRequestSignature($request);
        $maxAttempts = config('auth.security.rate_limit.login_attempts');
        $decayMinutes = config('auth.security.rate_limit.lockout_minutes');

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $this->limiter->availableIn($key);

            // en FR
            return response()->json([
                'message' => sprintf(
                    'Trop de tentatives de connexion. Veuillez réessayer dans %d minute(s).',
                    ceil($seconds / 60)
                ),
                'retry_after' => $seconds,
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Clear rate limit on successful authentication
        if ($response->isSuccessful()) {
            $this->limiter->clear($key);
        }

        return $response;
    }

    private function resolveRequestSignature(Request $request): string
    {
        return sprintf(
            'auth-login:%s:%s',
            sha1($request->ip()),
            sha1($request->input('email', ''))
        );
    }
}
