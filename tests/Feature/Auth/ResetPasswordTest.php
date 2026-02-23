<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Events\PasswordResetCompletedEvent;
use Domain\Auth\Models\PasswordResetToken;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use function Pest\Laravel\postJson;

pest()->group('auth');

beforeEach(function () {
    Cache::flush();
    $this->user = User::factory()->create();
});

// --- THROTTLE ---

it('throttles after max reset attempts', function () {
    $max = config('auth.security.rate_limit.login_attempts');

    for ($i = 0; $i < $max; $i++) {
        postJson(route('auth.password.reset'), [
            'email' => $this->user->email,
            'token' => Str::random(PasswordResetToken::TOKEN_LENGTH - 1),
            'password' => 'NewPassword1!',
            'password_confirmation' => 'NewPassword1!',
        ])->assertUnprocessable();
    }

    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH - 1),
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertTooManyRequests()
        ->assertJsonStructure(['message', 'retry_after']);
});

// --- VALIDATION ---

it('requires email, token, and password', function () {
    postJson(route('auth.password.reset'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'token', 'password']);
});

it('rejects invalid email format', function () {
    postJson(route('auth.password.reset'), [
        'email' => 'not-an-email',
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH),
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects token that is too short', function () {
    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH - 1),
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['token']);
});

it('rejects password shorter than 8 characters', function () {
    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH),
        'password' => 'short',
        'password_confirmation' => 'short',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('rejects mismatched password confirmation', function () {
    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH),
        'password' => 'NewPassword1!',
        'password_confirmation' => 'DifferentPassword1!',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

// --- BEHAVIOR ---

it('returns 204 on successful password reset', function () {
    $plainToken = createResetToken($this->user, confirmed: true);

    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => $plainToken,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertNoContent();
});

it('updates the user password in the database', function () {
    $plainToken = createResetToken($this->user, confirmed: true);

    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => $plainToken,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertNoContent();

    $this->user->refresh();

    expect(Hash::check('NewPassword1!', $this->user->password))->toBeTrue();
});

it('revokes all active sessions after password reset', function () {
    loginViaApi($this->user);

    expect(Session::query()->where('user_id', $this->user->id)->count())->toBe(1);

    $plainToken = createResetToken($this->user, confirmed: true);

    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => $plainToken,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertNoContent();

    expect(Session::query()->where('user_id', $this->user->id)->count())->toBe(0);
});

it('deletes the reset token after successful reset', function () {
    $plainToken = createResetToken($this->user, confirmed: true);

    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => $plainToken,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertNoContent();

    expect(
        PasswordResetToken::query()->where('email', $this->user->email)->count()
    )->toBe(0);
});

it('returns 400 when token does not exist', function () {
    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => Str::random(PasswordResetToken::TOKEN_LENGTH),
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertBadRequest();
});

it('returns 400 when token is expired', function () {
    $plainToken = createResetToken($this->user, confirmed: true, expired: true);

    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => $plainToken,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertBadRequest();
});

it('returns 400 when token has not been confirmed yet', function () {
    $plainToken = createResetToken($this->user);

    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => $plainToken,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertBadRequest();
});

// --- EVENTS ---

it('dispatches PasswordResetCompletedEvent with correct user', function () {
    Event::fake();

    $plainToken = createResetToken($this->user, confirmed: true);

    postJson(route('auth.password.reset'), [
        'email' => $this->user->email,
        'token' => $plainToken,
        'password' => 'NewPassword1!',
        'password_confirmation' => 'NewPassword1!',
    ])->assertNoContent();

    Event::assertDispatched(PasswordResetCompletedEvent::class, function (PasswordResetCompletedEvent $event): bool {
        return $event->user->id === $this->user->id;
    });
});
