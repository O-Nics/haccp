<?php

declare(strict_types=1);

namespace App\Core\Ensures;

use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\GoneException;
use App\Core\Exceptions\LockedException;
use App\Core\Exceptions\NotFoundException;

/**
 * Base class for domain ensure classes.
 *
 * Ensures encapsulate business rule checks that must pass before an action is executed.
 * Each method should represent a single guard condition and throw the appropriate
 * HTTP exception when the condition is violated.
 *
 * Available guards:
 * @see fail()        409 Conflict  — business rule violation (duplicate, constraint)
 * @see failNotFound() 404 Not Found — resource does not exist or does not belong to tenant
 * @see failGone()    410 Gone      — resource existed but has been soft-deleted
 * @see failLocked()  423 Locked    — resource exists but cannot be modified (frozen state)
 */
abstract class BaseEnsure
{
    /**
     * Throw a 409 Conflict exception.
     *
     * Use when a business rule is violated due to a conflicting state:
     * duplicate resource, constraint blocked by related data, etc.
     *
     * @throws BusinessException
     */
    protected function fail(string $message): void
    {
        throw new BusinessException($message);
    }

    /**
     * Throw a 404 Not Found exception.
     *
     * Use when a resource does not exist or does not belong to the current tenant.
     * Prefer this over 403 for cross-tenant access to avoid leaking resource existence.
     *
     * @throws NotFoundException
     */
    protected function failNotFound(string $message): void
    {
        throw new NotFoundException($message);
    }

    /**
     * Throw a 410 Gone exception.
     *
     * Use when a resource previously existed but has been soft-deleted.
     * Signals to the client that the deletion was intentional, not a missing record.
     *
     * @throws GoneException
     */
    protected function failGone(string $message): void
    {
        throw new GoneException($message);
    }

    /**
     * Throw a 423 Locked exception.
     *
     * Use when a resource exists and is accessible but cannot be mutated
     * because it has been locked or frozen by a business rule (e.g. validated period).
     *
     * @throws LockedException
     */
    protected function failLocked(string $message): void
    {
        throw new LockedException($message);
    }
}