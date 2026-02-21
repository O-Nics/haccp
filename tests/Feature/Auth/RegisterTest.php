<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Events\UserRegisteredEvent;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Domain\Auth\Notifications\UserRegisterNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\postJson;

pest()->group('auth');

beforeEach(function () {
    Cache::flush();
    $this->device = Device::factory()->make();
});

// --- THROTTLE ---

it('throttles after max register attempts', function () {
    $max = config('auth.security.rate_limit.login_attempts');
    $email = 'throttle@test.com';

    for ($i = 0; $i < $max; $i++) {
        postJson(route('auth.register'), [
            'email' => $email,
            'password' => 'tooweak',
            'password_confirmation' => 'tooweak',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'device_id' => $this->device->device_id,
            'device_platform' => $this->device->platform,
        ])->assertUnprocessable();
    }

    postJson(route('auth.register'), [
        'email' => $email,
        'password' => 'tooweak',
        'password_confirmation' => 'tooweak',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
    ])->assertTooManyRequests()
        ->assertJsonStructure(['message', 'retry_after']);
});

// --- VALIDATION ---

it('requires email, password, first_name, last_name, device_id and device_platform', function () {
    postJson(route('auth.register'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password', 'first_name', 'last_name', 'device_id', 'device_platform']);
});

it('rejects invalid email format', function () {
    postJson(route('auth.register'), [
        'email' => 'invalid-email',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects password that does not meet complexity requirements', function () {
    postJson(route('auth.register'), [
        'email' => 'user@example.com',
        'password' => 'tooweak',
        'password_confirmation' => 'tooweak',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('rejects mismatched password confirmation', function () {
    postJson(route('auth.register'), [
        'email' => 'user@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!different',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('rejects already taken email', function () {
    $existing = User::factory()->create();

    postJson(route('auth.register'), [
        'email' => $existing->email,
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

// --- BEHAVIOR ---

it('returns 201 with correct structure on valid registration', function () {
    postJson(route('auth.register'), [
        'email' => 'new@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'email', 'first_name', 'last_name'],
                'tokens' => ['access', 'refresh'],
            ],
        ]);
});

it('creates user, device and session on registration', function () {
    postJson(route('auth.register'), [
        'email' => 'new@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertCreated();

    $user = User::query()->where('email', 'new@example.com')->first();

    expect($user)->not->toBeNull()
        ->and(Device::query()->where('user_id', $user->id)->count())->toBe(1)
        ->and(Session::query()->where('user_id', $user->id)->count())->toBe(1);
});

// --- EVENTS & NOTIFICATIONS ---

it('dispatches UserRegisteredEvent on registration', function () {
    Event::fake();

    postJson(route('auth.register'), [
        'email' => 'new@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertCreated();

    Event::assertDispatched(UserRegisteredEvent::class);
});

it('sends UserRegisterNotification to the new user', function () {
    Notification::fake();

    postJson(route('auth.register'), [
        'email' => 'new@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertCreated();

    $user = User::query()->where('email', 'new@example.com')->first();

    Notification::assertSentTo($user, UserRegisterNotification::class);
});