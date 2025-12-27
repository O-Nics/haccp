<?php

declare(strict_types=1);

namespace Feature\Auth;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_check_auth(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $business = Business::factory()->withOrganisation()->withBusinessType()->create();

        $user->businesses()->attach($business->id);

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

      Sanctum::actingAs($user);
        $response = $this->postJson(route('auth.check-auth'), [
            'device_id' => 'test-device-001',
            'device_name' => 'iPhone 14 Pro',
            'device_type' => 'mobile',
            'platform' => 'ios',
            'browser' => 'Safari',
            'remember_me' => false,
            'active_business_id' => (string) $business->id,

        ]
        )->dump();

    }
}
