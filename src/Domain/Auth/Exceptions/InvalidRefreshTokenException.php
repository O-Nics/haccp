<?php

declare(strict_types=1);

namespace Domain\Auth\Exceptions;

use App\Core\Exceptions\BaseException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidRefreshTokenException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Invalid or expired refresh token', Response::HTTP_BAD_REQUEST);
    }
}
