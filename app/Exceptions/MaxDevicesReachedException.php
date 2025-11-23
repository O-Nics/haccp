<?php

// app/Exceptions/MaxDevicesReachedException.php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class MaxDevicesReachedException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Maximum device limit reached. Please revoke a device to continue.',
            Response::HTTP_FORBIDDEN
        );
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
