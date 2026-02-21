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
     * @param  string[]  $accessAbilities
     * @param  string[]  $refreshAbilities
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
     * @param  string[]  $abilities
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
