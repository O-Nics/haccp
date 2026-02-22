<?php

namespace App\Core\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessException extends BaseException
{
    public function __construct(
        string $message,
    ) {
        parent::__construct($message);
    }

    public function renderHttp(Request $request): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ])->setStatusCode(409);
    }
}
