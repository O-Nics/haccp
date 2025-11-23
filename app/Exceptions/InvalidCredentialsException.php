<?php

// app/Exceptions/InvalidCredentialsException.php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class InvalidCredentialsException extends Exception
{
    public function __construct(string $message = 'Email ou mot de passe invalide', int $code = Response::HTTP_UNAUTHORIZED)
    {
        parent::__construct($message, $code);
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
