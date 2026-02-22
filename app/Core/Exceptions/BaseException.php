<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseException extends Exception
{
    public function __construct(
        string $message,
        int $code = 500,
    ) {
        parent::__construct($message, $code);
    }

    public function render(?Request $request): ?JsonResponse
    {
        if ($request) {
            return $this->renderHttp($request);
        }

        // Jobs, Cron, Command
        return null;
    }

    public function renderHttp(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ])->setStatusCode($this->getCode() ?: 500);
    }
}
