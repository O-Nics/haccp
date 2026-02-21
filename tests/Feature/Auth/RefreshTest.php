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

// --- BEHAVIOR ---

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

it('issues new tokens different from the original ones', function () {
    $tokens = loginViaApi($this->user);

    $response = postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . $tokens['refresh'],
    ])->assertOk();

    expect($response->json('data.tokens.access.token'))->not->toBe($tokens['access'])
        ->and($response->json('data.tokens.refresh.token'))->not->toBe($tokens['refresh']);
});

it('revokes old session and creates a new one after refresh', function () {
    $tokens = loginViaApi($this->user);

    $sessionsBefore = Session::query()->where('user_id', $this->user->id)->count();

    postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . $tokens['refresh'],
    ])->assertOk();

    $sessionsAfter = Session::query()->where('user_id', $this->user->id)->count();

    expect($sessionsAfter)->toBe($sessionsBefore);
});

// --- ERROR CASES ---

it('returns 400 when refresh token has no associated session', function () {
    postJson(route('auth.refresh'), [], [
        'Authorization' => 'Bearer ' . refreshToken($this->user),
    ])->assertBadRequest();
});