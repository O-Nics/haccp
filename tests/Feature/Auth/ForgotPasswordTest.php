<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Events\PasswordResetRequestedEvent;
use Domain\Auth\Models\PasswordResetToken;
use Domain\Auth\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\postJson;

pest()->group('auth');

beforeEach(function () {
    Cache::flush();
    $this->user = User::factory()->create();
});

// --- THROTTLE ---

it('throttles after max password reset attempts', function () {
    $max = config('auth.security.rate_limit.login_attempts');

    for ($i = 0; $i < $max; $i++) {
        postJson(route('auth.password.forgot'), [
            'email' => 'notfound@example.com',
        ])->assertUnprocessable();
    }

    postJson(route('auth.password.forgot'), [
        'email' => 'notfound@example.com',
    ])->assertTooManyRequests()
        ->assertJsonStructure(['message', 'retry_after']);
});

// --- VALIDATION ---

it('requires email', function () {
    postJson(route('auth.password.forgot'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects invalid email format', function () {
    postJson(route('auth.password.forgot'), [
        'email' => 'not-an-email',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects email not registered in the system', function () {
    postJson(route('auth.password.forgot'), [
        'email' => 'ghost@example.com',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

// --- BEHAVIOR ---

it('returns 204 on valid email', function () {
    postJson(route('auth.password.forgot'), [
        'email' => $this->user->email,
    ])->assertNoContent();
});

it('creates a reset token in the database', function () {
    postJson(route('auth.password.forgot'), [
        'email' => $this->user->email,
    ])->assertNoContent();

    expect(
        PasswordResetToken::query()->where('email', $this->user->email)->count()
    )->toBe(1);
});

it('replaces existing token when a new request is made', function () {
    createResetToken($this->user);

    postJson(route('auth.password.forgot'), [
        'email' => $this->user->email,
    ])->assertNoContent();

    expect(
        PasswordResetToken::query()->where('email', $this->user->email)->count()
    )->toBe(1);
});

// --- EVENTS ---

it('dispatches PasswordResetRequestedEvent with correct user and token', function () {
    Event::fake();

    postJson(route('auth.password.forgot'), [
        'email' => $this->user->email,
    ])->assertNoContent();

    Event::assertDispatched(PasswordResetRequestedEvent::class, function (PasswordResetRequestedEvent $event): bool {
        $storedToken = PasswordResetToken::query()
            ->where('email', $this->user->email)
            ->first();

        return $event->user->id === $this->user->id
            && $event->token === $storedToken?->token;
    });
});