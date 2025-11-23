<?php

declare(strict_types=1);

namespace Tests\Feature\Device;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class DeviceDestroyTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_revoke_a_specific_device(): void
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
        $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-002',
            'device_name' => 'MacBook Pro',
            'remember_me' => false,
        ]);

        $this->assertCount(2, Device::all());

        // Revoke device 2 using device 1's token
        $response = $this->withToken($login1->json('tokens.access.token'))
            ->deleteJson(route('devices.destroy', ['deviceId' => 'device-002']));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Device revoked successfully']);

        $this->assertCount(1, Device::all());
        $this->assertEquals('device-001', Device::first()->device_id);
    }

    public function test_revoking_device_returns_404_for_non_existent_device(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => false,
        ]);

        $response = $this->withToken($loginResponse->json('tokens.access.token'))
            ->deleteJson(route('devices.destroy', ['deviceId' => 'non-existent']));

        $response->assertStatus(404)
            ->assertJson(['message' => 'Device not found']);
    }

    public function test_user_cannot_revoke_another_users_device(): void
    {
        $user1 = User::factory()->create([
            'email' => 'user1@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $user2 = User::factory()->create([
            'email' => 'user2@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // User 1 login
        $login1 = $this->postJson(route('auth.login'), [
            'email' => 'user1@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'user1-device',
            'device_name' => 'User 1 Device',
            'remember_me' => false,
        ]);

        // User 2 login
        $this->postJson(route('auth.login'), [
            'email' => 'user2@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'user2-device',
            'device_name' => 'User 2 Device',
            'remember_me' => false,
        ]);

        // User 1 tries to revoke User 2's device
        $response = $this->withToken($login1->json('tokens.access.token'))
            ->deleteJson(route('devices.destroy', ['deviceId' => 'user2-device']));

        $response->assertStatus(404);

        // User 2's device should still exist
        $this->assertTrue(Device::where('device_id', 'user2-device')->exists());
    }

    public function test_device_destroy_requires_authentication(): void
    {
        $response = $this->deleteJson(route('devices.destroy', ['deviceId' => 'device-001']));

        $response->assertStatus(401);
    }

    public function test_user_can_revoke_their_current_device(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => false,
        ]);

        $accessToken = $loginResponse->json('tokens.access.token');
        $deviceId = $loginResponse->json('device_id');

        $this->assertCount(1, Device::all());
        $this->assertCount(1, PersonalAccessToken::all());

        // Revoke current device
        $response = $this->withToken($accessToken)
            ->deleteJson(route('devices.destroy', ['deviceId' => $deviceId]));

        $response->assertStatus(200);

        // Device should be deleted
        $this->assertCount(0, Device::all());
    }
}
