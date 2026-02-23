<?php

declare(strict_types=1);

namespace App\Core\Ensures;


use App\Core\Exceptions\BusinessException;

class BaseEnsure
{
    /**
     * @throws BusinessException
     */
    protected function fail(string $message): void
    {
        throw new BusinessException($message);
    }
}
