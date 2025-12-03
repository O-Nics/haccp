<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RefreshTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_refresh_tokens_with_valid_refresh_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // Login to get tokens
        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'device_type' => 'mobile',
            'remember_me' => false,
        ]);

        $refreshToken = $loginResponse->json('data.tokens.refresh.token');
        $deviceId = 'test-device-001';

        // Refresh tokens
        $response = $this->postJson(route('auth.refresh'), [
            'device_id' => $deviceId,
            'refresh_token' => $refreshToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'tokens' => [
                        'access' => [
                            'token',
                            'type',
                            'expires_in',
                        ],
                        'refresh' => [
                            'token',
                            'expires_in',
                        ],
                    ],
                ],
            ]);

        // New refresh token should be different (rotation enabled)
        $newRefreshToken = $response->json('data.tokens.refresh.token');
        $this->assertNotEquals($refreshToken, $newRefreshToken);
    }

    public function test_refresh_token_rotation_updates_device_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // Login
        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => false,
        ]);

        $oldRefreshToken = $loginResponse->json('data.tokens.refresh.token');
        $deviceId = 'test-device-001';

        $oldDevice = Device::where('device_id', $deviceId)->first();
        $oldTokenHash = $oldDevice->refresh_token_hash;

        // Refresh
        $this->postJson(route('auth.refresh'), [
            'device_id' => $deviceId,
            'refresh_token' => $oldRefreshToken,
        ]);

        // Token hash should be different
        $newDevice = Device::where('device_id', $deviceId)->first();
        $this->assertNotEquals($oldTokenHash, $newDevice->refresh_token_hash);
    }

    public function test_refresh_fails_with_invalid_refresh_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // Login
        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => false,
        ]);

        $deviceId = 'test-device-001';

        // Try to refresh with invalid token (must be 64 chars to pass validation)
        $response = $this->postJson(route('auth.refresh'), [
            'device_id' => $deviceId,
            'refresh_token' => str_repeat('x', 64), // Invalid but valid length
        ]);

        $response->assertStatus(401);
    }

    public function test_refresh_fails_with_expired_refresh_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // Login
        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => false,
        ]);

        $refreshToken = $loginResponse->json('data.tokens.refresh.token');
        $deviceId = 'test-device-001';
        // Manually expire the token by setting the device's refresh_token_expires_at in the past
        $device = Device::where('device_id', $deviceId)->first();
        $device->token_expires_at = now()->subMinutes(1);
        $device->save();
        // Try to refresh
        $response = $this->postJson(route('auth.refresh'), [
            'device_id' => $deviceId,
            'refresh_token' => $refreshToken,
        ]);
        $response->assertStatus(401);

    }

    public function test_refresh_fails_with_non_existent_device(): void
    {
        $response = $this->postJson(route('auth.refresh'), [
            'device_id' => 'non-existent-device',
            'refresh_token' => str_repeat('x', 64), // Must be 64 chars to pass validation
        ]);

        $response->assertStatus(401);
    }

    public function test_old_refresh_token_cannot_be_reused_after_rotation(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // Login
        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => false,
        ]);

        $oldRefreshToken = $loginResponse->json('data.tokens.refresh.token');
        $deviceId = 'test-device-001';

        // First refresh - should succeed
        $refreshResponse = $this->postJson(route('auth.refresh'), [
            'device_id' => $deviceId,
            'refresh_token' => $oldRefreshToken,
        ]);

        $refreshResponse->assertStatus(200);

        // Try to use old token again - should fail
        $response = $this->postJson(route('auth.refresh'), [
            'device_id' => $deviceId,
            'refresh_token' => $oldRefreshToken,
        ]);

        $response->assertStatus(401);
    }
}
