<?php

namespace App\Core\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SecureHeaders
{
    /**
     * @var string[]
     */
    protected array $unwanted = [
        'X-Powered-By',
        'Server',
    ];

    /**
     * @template TRequest of Request
     * @template TResponse of Response
     *
     * @param  Request  $request
     * @return Request
     */
    public function handle($request, Closure $next)
    {
        $this->removeUnwantedHeaders();

        $response = $next($request);

        $this->setHeaders($response);

        return $response;
    }

    /**
     * Set headers.
     *
     * @param  Response  $response
     */
    protected function setHeaders($response): void
    {
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    /**
     * Remove unwanted headers.
     */
    private function removeUnwantedHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        foreach ($this->unwanted as $header) {
            header_remove($header);
        }
    }
}
