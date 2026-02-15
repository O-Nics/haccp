<?php

namespace Tests\Feature\Auth;

use Domain\Auth\Models\User;
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
        'device_id' => $device->deviceId,
        'device_platform' => $device->devicePlatform,
        'device_model' => $device->deviceModel,
        'remember_me' => $device->rememberMe,
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
        'device_id' => $this->getRandomDevice()->deviceId,
        'device_platform' => $this->getRandomDevice()->devicePlatform,
        'device_model' => $this->getRandomDevice()->deviceModel,
        'remember_me' => $this->getRandomDevice()->rememberMe,
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
        'device_id' => $this->getRandomDevice()->deviceId,
        'device_platform' => $this->getRandomDevice()->devicePlatform,
        'device_model' => $this->getRandomDevice()->deviceModel,
        'remember_me' => $this->getRandomDevice()->rememberMe,
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
