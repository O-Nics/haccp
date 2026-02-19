<?php

namespace Tests\Feature\Auth;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use function Pest\Laravel\postJson;

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
        ->dump()
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

