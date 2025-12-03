<?php

declare(strict_types=1);

namespace Tests\Feature\Device;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DeviceIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_all_their_devices(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // Login from multiple devices
        $login1 = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-001',
            'device_name' => 'iPhone 14 Pro',
            'device_type' => 'mobile',
            'platform' => 'ios',
            'remember_me' => false,
        ]);

        $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-002',
            'device_name' => 'MacBook Pro',
            'device_type' => 'desktop',
            'platform' => 'macos',
            'remember_me' => false,
        ]);

        $accessToken = $login1->json('data.tokens.access.token');

        $response = $this->withToken($accessToken)
            ->getJson(route('devices.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'device_id',
                        'device_name',
                        'device_type',
                        'platform',
                        'ip_address',
                        'last_used_at',
                        'token_expires_at',
                        'is_token_expired',
                    ],
                ],
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function test_devices_are_ordered_by_last_used(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // Login from device 1
        $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-001',
            'device_name' => 'Old Device',
            'remember_me' => false,
        ]);

        sleep(1); // Ensure different timestamps

        // Login from device 2 (more recent)
        $login2 = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-002',
            'device_name' => 'New Device',
            'remember_me' => false,
        ]);

        $response = $this->withToken($login2->json('data.tokens.access.token'))
            ->getJson(route('devices.index'));

        $response->assertStatus(200);

        $devices = $response->json('data');

        // Most recent should be first
        $this->assertEquals('device-002', $devices[0]['device_id']);
        $this->assertEquals('device-001', $devices[1]['device_id']);
    }

    public function test_device_index_requires_authentication(): void
    {
        $response = $this->getJson(route('devices.index'));

        $response->assertStatus(401);
    }

    public function test_user_only_sees_their_own_devices(): void
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

        // User 1 should only see their device
        $response = $this->withToken($login1->json('data.tokens.access.token'))
            ->getJson(route('devices.index'));
        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('user1-device', $response->json('data')[0]['device_id']);
    }

    public function test_device_index_shows_token_expiration_status(): void
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

        $response = $this->withToken($accessToken)
            ->getJson(route('devices.index'));

        $response->assertStatus(200);

        $device = $response->json('data')[0];
        $this->assertArrayHasKey('token_expires_at', $device);
        $this->assertArrayHasKey('is_token_expired', $device);
        $this->assertFalse($device['is_token_expired']);
    }
}
