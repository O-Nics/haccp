<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;

use function Pest\Laravel\postJson;

pest()->group('auth');

beforeEach(function () {
    $this->user = User::factory()->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request', function () {
    postJson(route('auth.refresh'))->assertUnauthorized();
});

it('rejects access token on refresh route', function () {
    postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertForbidden();
});

// --- BEHAVIOR (rotation enabled) ---

it('returns new token pair on valid refresh', function () {
    $tokens = loginViaApi($this->user);

    postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . $tokens['refresh'],
    ])->assertOk()
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'email', 'first_name', 'last_name'],
                'tokens' => ['access', 'refresh'],
            ],
        ]);
});

it('issues new tokens different from the original ones when rotation is enabled', function () {
    config(['auth.tokens.refresh.rotation' => true]);

    $tokens = loginViaApi($this->user);

    $response = postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . $tokens['refresh'],
    ])->assertOk();

    expect($response->json('data.tokens.access.token'))->not->toBe($tokens['access'])
        ->and($response->json('data.tokens.refresh.token'))->not->toBe($tokens['refresh']);
});

it('revokes old session and creates a new one after refresh when rotation is enabled', function () {
    config(['auth.tokens.refresh.rotation' => true]);

    $tokens = loginViaApi($this->user);

    $sessionsBefore = Session::query()->where('user_id', $this->user->id)->count();

    postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . $tokens['refresh'],
    ])->assertOk();

    $sessionsAfter = Session::query()->where('user_id', $this->user->id)->count();

    expect($sessionsAfter)->toBe($sessionsBefore);
});

// --- BEHAVIOR (rotation disabled) ---

it('issues a new access token but no new refresh token when rotation is disabled', function () {
    config(['auth.tokens.refresh.rotation' => false]);

    $tokens = loginViaApi($this->user);

    $response = postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . $tokens['refresh'],
    ])->assertOk();

    expect($response->json('data.tokens.access.token'))->not->toBe($tokens['access'])
        ->and($response->json('data.tokens.refresh.token'))->toBeNull();
});

it('keeps the same session after refresh when rotation is disabled', function () {
    config(['auth.tokens.refresh.rotation' => false]);

    $tokens = loginViaApi($this->user);

    $sessionBefore = Session::query()->where('user_id', $this->user->id)->first();

    postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . $tokens['refresh'],
    ])->assertOk();

    $sessionAfter = Session::query()->where('user_id', $this->user->id)->first();

    expect($sessionAfter->id)->toBe($sessionBefore->id)
        ->and($sessionAfter->refresh_token_id)->toBe($sessionBefore->refresh_token_id)
        ->and($sessionAfter->access_token_id)->not->toBe($sessionBefore->access_token_id);
});

it('still returns the refresh token expiry when rotation is disabled', function () {
    config(['auth.tokens.refresh.rotation' => false]);

    $tokens = loginViaApi($this->user);

    $response = postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . $tokens['refresh'],
    ])->assertOk();

    expect($response->json('data.tokens.refresh.expires_at'))->toBeInt()->toBeGreaterThan(0);
});

// --- ERROR CASES ---

it('returns 400 when refresh token has no associated session', function () {
    postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . refreshToken($this->user),
    ])->assertBadRequest();
});
