<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;

use function Pest\Laravel\postJson;

pest()->group('auth');

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->device = Device::factory()->make();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request', function (): void {
    postJson(route('auth.check-auth'))->assertUnauthorized();
});

it('rejects refresh token on check-auth route', function (): void {
    postJson(route('auth.check-auth'), [
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
    ], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- VALIDATION ---

it('requires device_id and device_platform', function (): void {
    postJson(route('auth.check-auth'), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['device_id', 'device_platform']);
});

// --- BEHAVIOR ---

it('returns correct structure on valid check-auth', function (): void {
    $tokens = loginViaApi($this->user, $this->device);

    postJson(route('auth.check-auth'), [
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
    ], [
        'Authorization' => 'Bearer '.$tokens['access'],
    ])->assertOk()
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'email', 'first_name', 'last_name'],
                'tokens' => ['access', 'refresh'],
            ],
        ]);
});

it('issues new tokens different from the original ones', function (): void {
    $tokens = loginViaApi($this->user, $this->device);

    $response = postJson(route('auth.check-auth'), [
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
    ], [
        'Authorization' => 'Bearer '.$tokens['access'],
    ])->assertOk();

    expect($response->json('data.tokens.access.token'))->not->toBe($tokens['access'])
        ->and($response->json('data.tokens.refresh.token'))->not->toBe($tokens['refresh']);
});

it('maintains single session per device after check-auth', function (): void {
    $tokens = loginViaApi($this->user, $this->device);

    $sessionsBefore = Session::query()->where('user_id', $this->user->id)->count();

    postJson(route('auth.check-auth'), [
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
    ], [
        'Authorization' => 'Bearer '.$tokens['access'],
    ])->assertOk();

    expect(Session::query()->where('user_id', $this->user->id)->count())->toBe($sessionsBefore);
});
