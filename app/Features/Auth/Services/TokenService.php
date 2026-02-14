<?php

// app/Services/Auth/TokenService.php

namespace App\Features\Auth\Services;

use Carbon\Carbon;
use Domain\Auth\Models\User;

final class TokenService
{
    /**
     * @param  string[]  $acAbilities
     * @param  string[]  $rtAbilities
     */
    public function issueFor(
        User $user,
        array $acAbilities,
        array $rtAbilities,
        bool $rememberMe = false,
    ): TokenPair {
        $access = $user->createToken('access_token', $acAbilities, Carbon::now()->addMinutes(config('auth.tokens.access.expiration')));
        $refresh = $user->createToken('refresh_token', $rtAbilities, Carbon::now()->addMinutes(config('auth.tokens.refresh.expiration')));

        return new TokenPair($access, $refresh);
    }
}
