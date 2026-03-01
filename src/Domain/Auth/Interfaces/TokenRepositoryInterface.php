<?php

declare(strict_types=1);

namespace Domain\Auth\Interfaces;

use Domain\Auth\Models\User;
use Domain\Auth\ValueObjects\AccessToken;
use Domain\Auth\ValueObjects\TokenPair;

interface TokenRepositoryInterface
{
    /**
     * Issues a Sanctum access+refresh token pair for the user.
     * The refresh expiration is extended when rememberMe is true.
     *
     * @param  User  $user  The user to issue tokens for.
     * @param  string[]  $accessAbilities  Abilities to assign to the access token.
     * @param  string[]  $refreshAbilities  Abilities to assign to the refresh token.
     * @param  bool  $rememberMe  Whether to use the extended refresh expiration.
     */
    public function issueFor(
        User $user,
        array $accessAbilities,
        array $refreshAbilities,
        bool $rememberMe = false,
    ): TokenPair;

    /**
     * Issues a single Sanctum access token for the user (used during token rotation).
     *
     * @param  User  $user  The user to issue a token for.
     * @param  string[]  $abilities  Abilities to assign to the token.
     */
    public function issueAccessTokenFor(
        User $user,
        array $abilities,
    ): AccessToken;
}
