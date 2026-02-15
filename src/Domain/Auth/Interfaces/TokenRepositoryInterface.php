<?php

declare(strict_types=1);

namespace Domain\Auth\Interfaces;

use Domain\Auth\Models\User;
use Domain\Auth\ValueObjects\TokenPair;

interface TokenRepositoryInterface
{
    /**
     * @param  string[]  $accessAbilities
     * @param  string[]  $refreshAbilities
     */
    public function issueFor(
        User $user,
        array $accessAbilities,
        array $refreshAbilities,
        bool $rememberMe = false,
    ): TokenPair;
}
