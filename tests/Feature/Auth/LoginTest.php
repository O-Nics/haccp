<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Events\NewDeviceDetectedEvent;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Domain\Auth\Notifications\NewDeviceLoginNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\postJson;

pest()->group('auth');

beforeEach(function () {
    Cache::flush();
    $this->user = User::factory()->create();
    $this->device = Device::factory()->make();
});

// --- THROTTLE ---

it('throttles after max login attempts', function () {
    $max = config('auth.security.rate_limit.login_attempts');

    for ($i = 0; $i < $max; $i++) {
        postJson(route('auth.login'), [
            'email' => $this->user->email,
            'password' => 'wrongpassword',
            'device_id' => $this->device->device_id,
            'device_platform' => $this->device->platform,
            'device_model' => $this->device->model,
            'remember_me' => false,
        ])->assertUnauthorized();
    }

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'wrongpassword',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->dump()->assertTooManyRequests()
        ->assertJsonStructure(['message', 'retry_after']);
});

// --- VALIDATION ---

it('requires email and password', function () {
    postJson(route('auth.login'), [
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

it('rejects invalid email format', function () {
    postJson(route('auth.login'), [
        'email' => 'invalid-email',
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires device_id and device_platform', function () {
    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['device_id', 'device_platform']);
});

// --- BEHAVIOR ---

it('returns 401 on wrong credentials', function () {
    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'wrongpassword',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertUnauthorized();
});

it('returns correct structure on valid login', function () {
    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertOk()
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'email', 'first_name', 'last_name'],
                'tokens' => ['access', 'refresh'],
            ],
        ]);

    expect($this->user->tokens)->toHaveCount(2);
});

it('creates one device and one session on login', function () {
    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertOk();

    expect(Device::query()->where('user_id', $this->user->id)->count())->toBe(1)
        ->and(Session::query()->where('user_id', $this->user->id)->count())->toBe(1);
});

it('creates separate devices and sessions for multiple devices', function () {
    $device2 = Device::factory()->make();

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertOk();

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $device2->device_id,
        'device_platform' => $device2->platform,
        'device_model' => $device2->model,
        'remember_me' => $device2->remember_me,
    ])->assertOk();

    expect(Device::query()->where('user_id', $this->user->id)->count())->toBe(2)
        ->and(Session::query()->where('user_id', $this->user->id)->count())->toBe(2);
});

it('extends refresh token expiry when remember_me is true', function () {
    $response = postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => true,
    ])->assertOk();

    $expiresAt = $response->json('data.tokens.refresh.expires_at');
    $expected = now()->addMinutes(config('auth.tokens.remember.expiration'))->getTimestamp();

    expect($expiresAt)
        ->toBeGreaterThanOrEqual($expected - 5)
        ->toBeLessThanOrEqual($expected + 5);
});

// --- EVENTS & NOTIFICATIONS ---

it('dispatches NewDeviceDetectedEvent when device is new', function () {
    Event::fake();

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertOk();

    Event::assertDispatched(NewDeviceDetectedEvent::class);
});

it('does not dispatch NewDeviceDetectedEvent when device is already known', function () {
    Event::fake();
    Device::factory()->withUser($this->user)->create([
        'device_id' => $this->device->device_id,
        'platform' => $this->device->platform,
    ]);

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertOk();

    Event::assertNotDispatched(NewDeviceDetectedEvent::class);
});

it('sends NewDeviceLoginNotification when device is new', function () {
    Notification::fake();

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => $this->device->remember_me,
    ])->assertOk();

    Notification::assertSentTo(
        $this->user,
        NewDeviceLoginNotification::class,
        function (NewDeviceLoginNotification $notification): bool {
            return $notification->device->model === $this->device->model;
        }
    );
});
