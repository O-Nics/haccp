<?php

namespace Tests\Feature\Auth;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use function Pest\Laravel\postJson;
use Domain\Auth\Notifications\UserRegisterNotification;
use Domain\Auth\Events\UserRegisteredEvent;

pest()->group('auth');

it('can register with valid data', function () {
    $device = Device::factory()->make();

    postJson(route('auth.register'), [
        'email' => 'test@test.fr',
        'password' => 'password1P!',
        'password_confirmation' => 'password1P!',
        'device_id' => $device->device_id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
        ])->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'user' ,
                'tokens',
            ],
        ]);
});

it('cannot register with invalid data', function () {
    postJson(route('auth.register'), [
        'email' => 'invalid-email',
        'password' => 'short',
        'password_confirmation' => 'short',
        'device_id' => '',
        'first_name' => '',
        'last_name' => '',
        'device_platform' => '',
    ])->assertUnprocessable()
    ->assertJsonValidationErrors(['email', 'password', 'device_id', 'first_name', 'last_name', 'device_platform']);
});

it('cannot register with already taken email', function () {
 $user  = User::factory()->create();

    $device = Device::factory()->make();
    postJson(route('auth.register'), [
        'email' => $user->email,
        'password' => 'password1P!',
        'password_confirmation' => 'password1P!',
        'device_id' => $device->device_id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertUnprocessable()
    ->assertJsonValidationErrors(['email']);
});


it('dispatch userRegisterEvent', function () {
    Event::fake();

    $device = Device::factory()->make();
    postJson(route('auth.register'), [
        'email' => 'test@test.fr',
        'password' => 'password1P!',
        'password_confirmation' => 'password1P!',
        'device_id' => $device->device_id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertCreated();

    Event::assertDispatched(UserRegisteredEvent::class);
});

it('send UserRegisterNotification notification', function () {
    Notification::fake();

    $device = Device::factory()->make();
    postJson(route('auth.register'), [
        'email' => 'test@test.fr',
        'password' => 'password1P!',
        'password_confirmation' => 'password1P!',
        'device_id' => $device->device_id,
        'first_name' => 'John',
        'last_name' => 'Doe',
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertCreated();

   $user = User::where('email', 'test@test.fr')->first();

    Notification::assertSentTo(
        $user,
        UserRegisterNotification::class
    );
});

it('test throttle', function () {
    $user = User::factory()->create();

    $device = Device::factory()->make();

    for ($i = 0; $i < 5; $i++) {
        postJson(route('auth.login'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
            'device_id' => $device->device_id,
            'device_platform' => $device->platform,
            'device_model' => $device->model,
            'remember_me' => $device->remember_me,
        ])->assertUnauthorized();
    }

    postJson(route('auth.login'), [
        'email' => $user->email,
        'password' => 'wrongpassword',
        'device_id' => $device->device_id,
        'device_platform' => $device->platform,
        'device_model' => $device->model,
        'remember_me' => $device->remember_me,
    ])->assertTooManyRequests()
        ->assertJsonStructure(['message', 'retry_after']);
});
