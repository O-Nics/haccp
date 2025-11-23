<?php

// app/Http/Middleware/ThrottleAuth.php

declare(strict_types=1);

namespace App\Http\Middleware;

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
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = config('auth.security.rate_limit.login_attempts');
        $decayMinutes = config('auth.security.rate_limit.lockout_minutes');

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $this->limiter->availableIn($key);

            return response()->json([
                'message' => sprintf(
                    'Too many login attempts. Please try again in %d minutes.',
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
