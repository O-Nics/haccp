<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HTTP 404 Not Found
 *
 * Thrown when a resource does not exist or does not belong to the current tenant.
 * Preferred over 403 for cross-tenant access to avoid leaking resource existence
 * to potential attackers (security through obscurity in multi-tenant context).
 *
 * Use cases:
 * - Accessing a role that belongs to a different business
 * - Accessing a member that does not exist in the requested business
 */
class NotFoundException extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 404);
    }

    public function renderHttp(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ])->setStatusCode(404);
    }
}
