<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HTTP 410 Gone
 *
 * Thrown when a resource previously existed but has been soft-deleted.
 * Unlike 404, this explicitly signals that the deletion was intentional and permanent.
 *
 * Use cases:
 * - Accessing a soft-deleted temperature record
 * - Accessing a revoked member
 * - Accessing a closed and deleted control period
 */
class GoneException extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 410);
    }

    public function renderHttp(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ])->setStatusCode(410);
    }
}