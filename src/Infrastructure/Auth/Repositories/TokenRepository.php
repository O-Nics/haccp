<?php

declare(strict_types=1);

namespace Infrastructure\Auth\Repositories;

use App\Core\Repositories\BaseRepository;
use Carbon\Carbon;
use Domain\Auth\Interfaces\TokenRepositoryInterface;
use Domain\Auth\Models\User;
use Domain\Auth\ValueObjects\TokenPair;

final readonly class TokenRepository extends BaseRepository implements TokenRepositoryInterface
{
    public function __construct(
        private User $model
    ) {}

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

        $refresh = $user->createToken(
            'refresh_token',
            $refreshAbilities,
            Carbon::now()->addMinutes(config('auth.tokens.refresh.expiration'))
        );

        return new TokenPair($access, $refresh);
    }
}
