<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Models\PasswordResetToken;
use Domain\Auth\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use function Pest\Laravel\postJson;

pest()->group('auth');

beforeEach(function () {
    Cache::flush();
    $this->user = User::factory()->create();
});

// --- THROTTLE ---

it('throttles after max confirm attempts', function () {
    $max = config('auth.security.rate_limit.login_attempts');

    for ($i = 0; $i < $max; $i++) {
        postJson(route('auth.password.confirm'), [
            'email' => $this->user->email,
            'token' => Str::random(PasswordResetToken::TOKEN_LENGTH - 1),
        ])->assertUnprocessable();
    }

    postJson(route('auth.password.confirm'), [
        'email' => $this->user->email,
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH - 1),
    ])->assertTooManyRequests()
        ->assertJsonStructure(['message', 'retry_after']);
});

// --- VALIDATION ---

it('requires email and token', function () {
    postJson(route('auth.password.confirm'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'token']);
});

it('rejects invalid email format', function () {
    postJson(route('auth.password.confirm'), [
        'email' => 'not-an-email',
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects token that is too short', function () {
    postJson(route('auth.password.confirm'), [
        'email' => $this->user->email,
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH - 1),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['token']);
});

it('rejects token that is too long', function () {
    postJson(route('auth.password.confirm'), [
        'email' => $this->user->email,
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH + 1),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['token']);
});

// --- BEHAVIOR ---

it('returns 204 when token is valid', function () {
    $plainToken = createResetToken($this->user);

    postJson(route('auth.password.confirm'), [
        'email' => $this->user->email,
        'token' => $plainToken,
    ])->assertNoContent();
});

it('marks the token as confirmed in the database', function () {
    $plainToken = createResetToken($this->user);

    postJson(route('auth.password.confirm'), [
        'email' => $this->user->email,
        'token' => $plainToken,
    ])->assertNoContent();

    $resetToken = PasswordResetToken::query()
        ->where('email', $this->user->email)
        ->first();

    expect($resetToken->isConfirmed())->toBeTrue();
});

it('returns 400 when token does not exist', function () {
    postJson(route('auth.password.confirm'), [
        'email' => $this->user->email,
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH),
    ])->assertBadRequest();
});

it('returns 400 when token is expired', function () {
    $plainToken = createResetToken($this->user, expired: true);

    postJson(route('auth.password.confirm'), [
        'email' => $this->user->email,
        'token' => $plainToken,
    ])->assertBadRequest();
});

it('returns 400 when token is already confirmed', function () {
    $plainToken = createResetToken($this->user, confirmed: true);

    postJson(route('auth.password.confirm'), [
        'email' => $this->user->email,
        'token' => $plainToken,
    ])->assertBadRequest();
});