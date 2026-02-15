<?php

declare(strict_types=1);

namespace Domain\Auth\Exceptions;

use App\Core\Exceptions\BaseException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidCredentialsException extends BaseException
{
    public function __construct(string $message = 'Email ou mot de passe invalide', int $code = Response::HTTP_UNAUTHORIZED)
    {
        parent::__construct($message, $code);
    }
}
