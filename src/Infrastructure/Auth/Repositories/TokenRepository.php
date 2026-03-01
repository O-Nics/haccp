<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Repositories;

use App\Core\Repositories\BaseRepository;
use Carbon\Carbon;
use Domain\Auth\Interfaces\TokenRepositoryInterface;
use Domain\Auth\Models\User;
use Domain\Auth\ValueObjects\AccessToken;
use Domain\Auth\ValueObjects\TokenPair;

final readonly class TokenRepository extends BaseRepository implements TokenRepositoryInterface
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
    ): TokenPair {
        $access = $user->createToken(
            'access_token',
            $accessAbilities,
            Carbon::now()->addMinutes(config('auth.tokens.access.expiration'))
        );

        $refreshExpiration = $rememberMe
            ? config('auth.tokens.remember.expiration')
            : config('auth.tokens.refresh.expiration');

        $refresh = $user->createToken(
            'refresh_token',
            $refreshAbilities,
            Carbon::now()->addMinutes($refreshExpiration)
        );

        return new TokenPair(
            accessToken: $access->plainTextToken,
            accessTokenId: $access->accessToken->id,
            accessExpiresAt: $access->accessToken->expires_at->getTimestamp(),
            refreshToken: $refresh->plainTextToken,
            refreshTokenId: $refresh->accessToken->id,
            refreshExpiresAt: $refresh->accessToken->expires_at->getTimestamp(),
        );
    }

    /**
     * Issues a single Sanctum access token for the user (used during token rotation).
     *
     * @param  User  $user  The user to issue a token for.
     * @param  string[]  $abilities  Abilities to assign to the token.
     */
    public function issueAccessTokenFor(
        User $user,
        array $abilities,
    ): AccessToken {
        $newAccessToken = $user->createToken(
            'access_token',
            $abilities,
            Carbon::now()->addMinutes(config('auth.tokens.access.expiration'))
        );

        return new AccessToken(
            plainToken: $newAccessToken->plainTextToken,
            tokenId: $newAccessToken->accessToken->id,
            expiresAt: $newAccessToken->accessToken->expires_at->getTimestamp(),
        );
    }
}
