<?php

declare(strict_types=1);

namespace Tests\Feature\Device;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class DeviceRevokeOthersTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_revoke_all_devices_except_current(): void
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

        // Login from device 3
        $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-003',
            'device_name' => 'iPad Pro',
            'remember_me' => false,
        ]);

        $this->assertCount(3, Device::all());

        // Revoke all except device-001
        $response = $this->withToken($login1->json('data.tokens.access.token'))
            ->postJson(route('devices.revoke-others'), [
                'current_device_id' => 'device-001',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'revoked_count',
            ]);

        $this->assertEquals(2, $response->json('revoked_count'));
        $this->assertCount(1, Device::all());
        $this->assertEquals('device-001', Device::first()->device_id);
    }

    public function test_revoke_others_also_deletes_associated_access_tokens(): void
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

        $this->assertCount(2, PersonalAccessToken::all());
        // Revoke all except device-001
        $this->withToken($login1->json('data.tokens.access.token'))
            ->postJson(route('devices.revoke-others'), [
                'current_device_id' => 'device-001',
            ]);

        // Only device-001's token should remain
        $this->assertCount(1, PersonalAccessToken::all());
        $this->assertEquals('device-001', PersonalAccessToken::first()->name);
    }

    public function test_revoke_others_requires_current_device_id(): void
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

        $response = $this->withToken($loginResponse->json('data.tokens.access.token'))
            ->postJson(route('devices.revoke-others'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_device_id']);
    }

    public function test_revoke_others_requires_authentication(): void
    {
        $response = $this->postJson(route('devices.revoke-others'), [
            'current_device_id' => 'device-001',
        ]);

        $response->assertStatus(401);
    }

    public function test_revoke_others_with_only_one_device_returns_zero_revoked(): void
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


        $this->assertCount(1, Device::all());

        // Revoke all except current (which is the only one)
        $response = $this->withToken($loginResponse->json('data.tokens.access.token'))
            ->postJson(route('devices.revoke-others'), [
                'current_device_id' => 'device-001',
            ]);

        $response->assertStatus(200);
        $this->assertEquals(0, $response->json('revoked_count'));
        $this->assertCount(1, Device::all());
    }
}
