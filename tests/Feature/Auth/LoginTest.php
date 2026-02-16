<?php

namespace Tests\Feature\Auth;

use Domain\Auth\Events\NewDeviceDetectedEvent;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Domain\Auth\Notifications\NewDeviceLoginNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestHelpers\DeviceHelper;

use function Pest\Laravel\postJson;

pest()->group('auth');

uses(DeviceHelper::class);

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@email.com',
    ]);

    $device = $this->getRandomDevice();

    postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_id' => $device->device_id,
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'email',
                    'first_name',
                    'last_name',
                ],
                'tokens',
            ],
        ]);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->tokens)->toHaveCount(2);
});

it('cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@good.fr',
    ]);

    postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'wrongpassword',
        'device_id' => 'device-id',
        'device_platform' => 'device-platform',
        'device_model' => 'device-model',
        'remember_me' => false,
    ])->assertUnauthorized();
});

it('cannot login with missing fields', function () {
    postJson(route('auth.login'), [
        'device_id' => $this->getRandomDevice()->deviceId,
        'device_platform' => $this->getRandomDevice()->devicePlatform,
        'device_model' => $this->getRandomDevice()->deviceModel,
        'remember_me' => $this->getRandomDevice()->rememberMe,
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

it('cannot login with invalid email format', function () {
    postJson(route('auth.login'), [
        'email' => 'invalid-email',
        'password' => 'password',
        'device_id' => 'device-id',
        'device_platform' => 'device-platform',
        'device_model' => 'device-model',
        'remember_me' => false,
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('cannot login without device information', function () {
    postJson(route('auth.login'), [
        'email' => 't@t.fr',
        'password' => 'password',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['device_id', 'device_platform']);
});

it('can login and have device & session un storage', function () {
    $user = User::factory()->create([
        'email' => 'test@email.com',
    ]);

    $device = $this->getRandomDevice();

    postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_id' => $device->device_id,
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertOk();

    $sessions = Session::query()->where('user_id', $user->id)->get();
    $devices = Device::query()->where('user_id', $user->id)->get();
    expect($sessions)->toHaveCount(1)->and($devices)->toHaveCount(1);
});

it('can login and have event dispatched if new device', function () {
    Event::fake();
    $user = User::factory()->create([
        'email' => 'test@email.com',
    ]);

    $device = $this->getRandomDevice();

    postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_id' => $device->device_id,
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertOk();

    Event::assertDispatched(NewDeviceDetectedEvent::class);
});

it('can login and have notfication sent if new device', function () {
    Notification::fake();
    $user = User::factory()->create([
        'email' => 'test@email.com',
    ]);

    $device = $this->getRandomDevice();

    postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_id' => $device->device_id,
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertOk();

    Notification::assertSentTo(
        $user,
        NewDeviceLoginNotification::class,
        function ($notification, $channels) use ($device) {
            return $notification->device->model === $device->model;
        }
    );
});

it('can login and have event not dispatched if same device', function () {
    Event::fake();
    $user = User::factory()->create([
        'email' => 'test@email.com',
    ]);

   $device = Device::factory()->withUser($user)->create();

    postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_id' => $device->device_id,
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertOk();

    Event::assertNotDispatched(NewDeviceDetectedEvent::class);
});


it('can login and have multiple device & session un storage', function () {
    $user = User::factory()->create([
        'email' => 'test@email.com',
    ]);

    $device = $this->getRandomDevice();
    $device2 = $this->getRandomDevice();

    postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_id' => $device->device_id,
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertOk();

    postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_id' => $device2->device_id,
        'device_platform' => $device2->platform,
        'device_model' => $device2->model,
        'remember_me' => $device2->remember_me,
    ])->assertOk();

    $sessions = Session::query()->where('user_id', $user->id)->get();
    $devices = Device::query()->where('user_id', $user->id)->get();
    expect($sessions)->toHaveCount(2)->and($devices)->toHaveCount(2);
});
