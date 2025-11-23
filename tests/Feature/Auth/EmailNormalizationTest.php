<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmailNormalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_normalizes_email_to_lowercase(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'email' => 'TEST@EXAMPLE.COM',
            'password' => 'SecureP@ss123',
            'first_name' => 'Test',
            'last_name' => 'User',
            'password_confirmation' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'Test Device',
            'remember_me' => false,
        ]);

        $response->assertStatus(201);

        // Verify email was stored in lowercase
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'TEST@EXAMPLE.COM',
        ]);
    }

    public function test_register_trims_email_whitespace(): void
    {
        $response = $this->postJson(route('auth.register'), [
            'email' => '  test@example.com  ',
            'first_name' => 'Test',
            'last_name' => 'User',
            'password' => 'SecureP@ss123',
            'password_confirmation' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'Test Device',
            'remember_me' => false,
        ]);

        $response->assertStatus(201);

        // Verify email was trimmed
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_login_normalizes_email_to_lowercase(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => 'TEST@EXAMPLE.COM',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'Test Device',
            'remember_me' => false,
        ]);

        $response->assertStatus(200);
    }

    public function test_login_trims_email_whitespace(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => '  test@example.com  ',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'Test Device',
            'remember_me' => false,
        ]);

        $response->assertStatus(200);
    }

    public function test_login_with_mixed_case_and_whitespace(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $response = $this->postJson(route('auth.login'), [
            'email' => '  TEST@Example.COM  ',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'Test Device',
            'remember_me' => false,
        ]);

        $response->assertStatus(200);
    }

    public function test_forgot_password_normalizes_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->postJson(route('auth.password.forgot'), [
            'email' => '  TEST@EXAMPLE.COM  ',
        ]);

        $response->assertStatus(200);

        // Verify token was created with normalized email
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }
}
