<?php

declare(strict_types=1);

namespace Feature\Auth\Password;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ConfirmPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_confirm_valid_reset_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $token = PasswordResetToken::generateToken();

        $resetToken = new PasswordResetToken;
        $resetToken->email = 'test@example.com';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addHour();
        $resetToken->save();

        $response = $this->postJson(route('password.confirm'), [
            'email' => 'test@example.com',
            'token' => $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Token confirmed successfully. You may now reset your password.',
            ]);

        // Check token is marked as confirmed
        $resetToken->refresh();
        $this->assertNotNull($resetToken->confirmed_at);
    }

    public function test_confirm_fails_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = PasswordResetToken::generateToken();

        $resetToken = new PasswordResetToken;
        $resetToken->email = 'test@example.com';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addHour();
        $resetToken->save();

        $response = $this->postJson(route('password.confirm'), [
            'email' => 'test@example.com',
            'token' => 'invalid-token-abc123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['token']);
    }

    public function test_confirm_fails_with_expired_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = PasswordResetToken::generateToken();

        $resetToken = new PasswordResetToken;
        $resetToken->email = 'test@example.com';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->subMinute(); // Expired
        $resetToken->save();

        $response = $this->postJson(route('password.confirm'), [
            'email' => 'test@example.com',
            'token' => $token,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Reset token has expired',
            ]);
    }

    public function test_confirm_fails_with_already_confirmed_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $token = PasswordResetToken::generateToken();

        $resetToken = new PasswordResetToken;
        $resetToken->email = 'test@example.com';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addHour();
        $resetToken->confirmed_at = now(); // Already confirmed
        $resetToken->save();

        $response = $this->postJson(route('password.confirm'), [
            'email' => 'test@example.com',
            'token' => $token,
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Reset token already used',
            ]);
    }

    public function test_confirm_fails_with_non_existent_token(): void
    {
        $response = $this->postJson(route('password.confirm'), [
            'email' => 'test@example.com',
            'token' => PasswordResetToken::generateToken(),
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid reset token',
            ]);
    }
}
