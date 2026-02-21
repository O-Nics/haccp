<?php

declare(strict_types=1);

use Domain\Auth\Enums\TokenAbilityEnum;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Illuminate\Testing\TestResponse;

use function Pest\Laravel\postJson;

/**
 * Create an access token for a user (ability: access).
 * ⚠ No DB session created, so auth/refresh will fail if used with this token.
 * (Unless a session is manually created in DB for the user, which is what loginViaApi does).
 *
 * Usage:
 *   withToken(accessToken($user))->postJson(route('user.show'))
 */
function accessToken(User $user): string
{
    return $user->createToken('access_token', [TokenAbilityEnum::ACCESS->value])->plainTextToken;
}

/**
 * Create a refresh token for a user (ability: refresh).
 * ⚠ No DB session created, so auth/refresh will fail if used with this token
 * (unless a session is manually created in DB for the user, which is what loginViaApi does).
 *
 * Usage:
 *   withToken(refreshToken($user))->postJson(route('auth.refresh'))
 */
function refreshToken(User $user): string
{
    return $user->createToken('refresh_token', [TokenAbilityEnum::REFRESH->value])->plainTextToken;
}

/**
 * Simulate login via API and return the access and refresh tokens.
 * Creates a DB session for the user, so tokens can be used for auth/refresh/logout
 * and will be properly revoked on logout/refresh.
 *
 * Usage:
 *   $tokens = loginViaApi($user);
 *   withToken($tokens['access'])->postJson(route('auth.logout'));
 *   withToken($tokens['refresh'])->postJson(route('auth.refresh'));
 *
 * @return array{access: string, refresh: string, response: TestResponse}
 */
function loginViaApi(User $user, ?Device $device = null): array
{
    $device ??= Device::factory()->make();

    $response = postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_id' => $device->device_id,
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => false,
    ]);

    return [
        'access' => $response->json('data.tokens.access.token'),
        'refresh' => $response->json('data.tokens.refresh.token'),
        'response' => $response,
    ];
}
