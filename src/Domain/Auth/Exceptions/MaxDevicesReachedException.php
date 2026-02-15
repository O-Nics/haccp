<?php

declare(strict_types=1);

namespace Domain\Auth\Exceptions;

use App\Core\Exceptions\BaseException;
use Symfony\Component\HttpFoundation\Response;

final class MaxDevicesReachedException extends BaseException
{
    public function __construct()
    {
        parent::__construct(
            'Maximum device limit reached. Please revoke a device to continue.',
            Response::HTTP_FORBIDDEN
        );
    }
}
