<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class IdentifyRequest
{
    public const string HeaderName = 'X-Request-Id';

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($requestId = $request->header(static::HeaderName)) {
            if (! str($requestId)->isUlid()) {
                abort(400, 'Bad Request ID');
            }
        }

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set(static::HeaderName, Str::uuid(), true);

        return $response;
    }
}
