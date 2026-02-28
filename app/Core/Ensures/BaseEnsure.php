<?php

declare(strict_types=1);

namespace App\Core\Ensures;

use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;

class BaseEnsure
{
    /**
     * @throws BusinessException
     */
    protected function fail(string $message): void
    {
        throw new BusinessException($message);
    }

    /**
     * @throws NotFoundException
     */
    protected function failNotFound(string $message): void
    {
        throw new NotFoundException($message);
    }
}
