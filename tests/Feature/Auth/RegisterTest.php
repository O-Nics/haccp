<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Device;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'first_name' => 'Test',
            'last_name' => 'User',
            'password_confirmation' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'device_type' => 'mobile',
            'platform' => 'ios',
            'browser' => 'Safari',
            'remember_me' => false,
        ]);

        $response->assertSuccessful()->assertJsonStructure([
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

        $this->assertCount(1, User::all());
        $this->assertCount(1, Device::all());

        $user = User::first();
        $this->assertEquals('test@example.com', $user->email);
        $this->assertCount(1, $user->devices);
    }

    public function test_user_can_register_with_remember_me_enabled(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'password' => 'SecureP@ss123',
            'password_confirmation' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'device_type' => 'mobile',
            'remember_me' => true,
        ]);

        $response->assertSuccessful();

        // Remember token should have 1 year expiration (525600 minutes = 31536000 seconds)
        $expiresIn = $response->json('data.tokens.refresh.expires_in');
        $this->assertEquals(525600 * 60, $expiresIn);
    }

    public function test_registration_fails_with_invalid_email(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'email' => 'invalid-email',
            'password' => 'SecureP@ss123',
            'password_confirmation' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $this->assertCount(0, User::all());
    }

    public function test_registration_fails_with_weak_password(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'email' => 'test@example.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        $this->assertCount(0, User::all());
    }

    public function test_registration_fails_without_device_information(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'first_name' => 'Test',
            'last_name' => 'User',
            'password_confirmation' => 'SecureP@ss123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['device_id', 'device_name']);

        $this->assertCount(0, User::all());
    }

    public function test_registration_with_not_suspicious_login_event_sent()
    {
        Event::fake();

        $response = $this->postJson(route('auth.register'), [
            'email' => 'test@example.com',
            'password' => 'P@ssw0rd123',
            'password_confirmation' => 'P@ssw0rd123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
        ]);

        Event::assertNotDispatched(\App\Events\NewDeviceDetected::class);

    }
}
