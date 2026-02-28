<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * HTTP 423 Locked
 *
 * Thrown when a resource exists and is accessible, but cannot be modified
 * because it has been locked or frozen by a business rule.
 *
 * Use cases:
 * - Editing a control period that has already been validated and signed off
 * - Modifying a HACCP record that has been submitted to authorities
 * - Updating a checklist that has been officially closed
 */
class LockedException extends BaseException
{
    public function __construct(string $message)
    {
        parent::__construct($message, 423);
    }

    public function renderHttp(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ])->setStatusCode(423);
    }
}