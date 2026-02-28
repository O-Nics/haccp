<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HTTP 409 Conflict
 *
 * Thrown when a business rule is violated due to a conflicting state.
 *
 * Use cases:
 * - Creating a resource that already exists (duplicate name, slug, etc.)
 * - Deleting a resource that is still referenced by related data
 * - Inviting a user who is already a member of the business
 */
class BusinessException extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 409);
    }

    public function renderHttp(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ])->setStatusCode(409);
    }
}
