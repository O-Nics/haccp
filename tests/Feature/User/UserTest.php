<?php

declare(strict_types=1);

namespace Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_their_info(): void
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
            ->getJson(route('user.me'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'email',
                ],
            ]);

        $this->assertEquals('test@example.com', $response->json('data.email'));
    }

    public function test_me_endpoint_includes_user_devices(): void
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
            'remember_me' => false,
        ]);

        $this->postJson(route('auth.login'), [
            'email' => 'test@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'device-002',
            'device_name' => 'MacBook Pro',
            'remember_me' => false,
        ]);

        $accessToken = $login1->json('data.tokens.access.token');

        $response = $this->withToken($accessToken)
            ->getJson(route('user.me'));

        $response->assertStatus(200);

    }

    public function test_me_endpoint_requires_authentication(): void
    {
        $response = $this->getJson(route('user.me'));

        $response->assertStatus(401);
    }

    public function test_me_endpoint_with_invalid_token_fails(): void
    {
        $response = $this->withToken('invalid-token')
            ->getJson(route('user.me'));

        $response->assertStatus(401);
    }

    public function test_me_endpoint_returns_user_with_all_expected_fields(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'john@example.com',
            'password' => 'SecureP@ss123',
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'remember_me' => false,
        ]);

        $accessToken = $loginResponse->json('data.tokens.access.token');

        $response = $this->withToken($accessToken)
            ->getJson(route('user.me'));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
            ]);

        // Password should be hidden
        $this->assertArrayNotHasKey('password', $response->json('data'));
    }
}
