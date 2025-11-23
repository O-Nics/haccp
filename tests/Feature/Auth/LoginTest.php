<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'device_type' => 'mobile',
            'platform' => 'ios',
            'browser' => 'Safari',
            'remember_me' => false,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
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
            ]);

        $this->assertCount(1, Device::all());
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_login_with_new_device_dispatches_suspicious_login_event(): void
    {
        Event::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $resonse = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'new-device-001',
            'device_name' => 'New Device',
            'device_type' => 'desktop',
            'remember_me' => false,
        ]);

        Event::assertDispatched(\App\Events\NewDeviceDetected::class);
    }

    public function test_login_with_existing_device_updates_device_info(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // First login
        $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'device_type' => 'mobile',
            'remember_me' => false,
        ]);

        $this->assertCount(1, Device::all());

        // Second login with same device but different name
        $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 15 Pro Max',
            'device_type' => 'mobile',
            'remember_me' => false,
        ]);

        // Should still be 1 device, but updated
        $this->assertCount(1, Device::all());
        $device = Device::first();

        $this->assertEquals('iPhone 15 Pro Max', $device->device_name);
    }

    public function test_login_with_existing_device_add_new_device_info(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        // First login
        $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'device_type' => 'mobile',
            'remember_me' => false,
        ]);

        $this->assertCount(1, Device::all());

        // Second login with same device but different name
        $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-002',
            'device_name' => 'iPhone 15 Pro Max',
            'device_type' => 'mobile',
            'remember_me' => false,
        ]);

        // Should still be 1 device, but updated
        $this->assertCount(2, Device::all());
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'WrongPassword123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
        ]);

        $response->assertStatus(401);
        $this->assertCount(0, Device::all());
    }

    public function test_login_with_remember_me_uses_extended_token_expiration(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => true,
        ]);

        $response->assertStatus(200);

        // Remember token should have 1 year expiration
        $expiresIn = $response->json('tokens.refresh.expires_in');
        $this->assertEquals(525600 * 60, $expiresIn); // 1 year in seconds
    }
}
