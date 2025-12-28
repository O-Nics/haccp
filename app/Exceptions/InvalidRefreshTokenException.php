<?php

// app/Exceptions/InvalidRefreshTokenException.php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class InvalidRefreshTokenException extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid or expired refresh token', Response::HTTP_BAD_REQUEST);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
