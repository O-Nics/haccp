<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_logout_and_revoke_device(): void
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

        $accessToken = $loginResponse->json('data.tokens.access.token');
        $deviceId = 'test-device-001';

        $this->assertCount(1, Device::all());
        $this->assertCount(1, PersonalAccessToken::all());

        // Logout
        $response = $this->withToken($accessToken)
            ->postJson(route('user.logout'), [
                'device_id' => $deviceId,
            ]);

        $response->assertStatus(200);

        // Device and tokens should be deleted
        $this->assertCount(0, Device::all());
        $this->assertCount(0, PersonalAccessToken::all());
    }

    public function test_logout_requires_authentication(): void
    {
        $response = $this->postJson(route('user.logout'), [
            'device_id' => 'test-device-001',
        ]);

        $response->assertStatus(401);
    }

    public function test_logout_only_revokes_specified_device(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // Login from device 1
        $login1 = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => false,
        ]);

        // Login from device 2
        $login2 = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-002',
            'device_name' => 'MacBook Pro',
            'remember_me' => false,
        ]);

        $this->assertCount(2, Device::all());

        // Logout from device 1
        $this->withToken($login1->json('data.tokens.access.token'))
            ->postJson(route('user.logout'), [
                'device_id' => 'device-001',
            ]);

        // Only device 2 should remain
        $this->assertCount(1, Device::all());
        $this->assertEquals('device-002', Device::first()->device_id);
    }

    public function test_logout_fails_with_invalid_device_id(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => false,
        ]);

        $accessToken = $loginResponse->json('data.tokens.access.token');

        // Try to logout with wrong device id
        $response = $this->withToken($accessToken)
            ->postJson(route('user.logout'), [
                'device_id' => 'wrong-device-id',
            ]);

        // Should still return 200 (logout is idempotent)
        $response->assertStatus(200);

        // Original device should still exist
        $this->assertCount(1, Device::all());
    }

    public function test_logout_deletes_all_access_tokens_for_device(): void
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

        $accessToken = $loginResponse->json('data.tokens.access.token');
        $deviceId = 'test-device-001';

        $this->assertCount(1, PersonalAccessToken::where('name', 'test-device-001')->get());

        // Logout
        $this->withToken($accessToken)
            ->postJson(route('user.logout'), [
                'device_id' => $deviceId,
            ]);

        // All tokens for this device should be deleted
        $this->assertCount(0, PersonalAccessToken::where('name', 'test-device-001')->get());
    }

    public function test_logout_cannon_login_with_same_access_token(): void
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

        $accessToken = $loginResponse->json('data.tokens.access.token');
        $deviceId = 'test-device-001';

        // Verify token exists before logout
        $this->assertEquals(1, PersonalAccessToken::count());

        // Logout
        $this->withToken($accessToken)
            ->postJson(route('user.logout'), [
                'device_id' => $deviceId,
            ]);

        // Verify token was deleted from database
        $this->assertEquals(0, PersonalAccessToken::count(), 'Token should be deleted from database');

        // Clear auth to ensure fresh authentication check
        auth()->forgetGuards();

        // Try to access protected route with same token (should fail because token is deleted)
        $response = $this->withToken($accessToken)
            ->getJson(route('user.me'));
        $response->assertStatus(401);

    }
}
