<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;

use function Pest\Laravel\postJson;

pest()->group('auth');

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request', function (): void {
    postJson(route('auth.logout'))->assertUnauthorized();
});

it('rejects refresh token on logout route', function (): void {
    postJson(route('auth.logout'), [], [
        'Authorization' => 'Bearer ' . refreshToken($this->user),
    ])->assertForbidden();
});

// --- BEHAVIOR ---

it('returns 204 on successful logout', function (): void {
    $tokens = loginViaApi($this->user);

    postJson(route('auth.logout'), [], [
        'Authorization' => 'Bearer ' . $tokens['access'],
    ])->assertNoContent();
});

it('deletes session and tokens from db on logout', function (): void {
    $tokens = loginViaApi($this->user);

    postJson(route('auth.logout'), [], [
        'Authorization' => 'Bearer ' . $tokens['access'],
    ])->assertNoContent();

    expect(Session::query()->where('user_id', $this->user->id)->count())->toBe(0)
        ->and($this->user->tokens()->count())->toBe(0);
});