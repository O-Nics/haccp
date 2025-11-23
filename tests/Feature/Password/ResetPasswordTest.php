<?php

declare(strict_types=1);

namespace Tests\Feature\Password;

use App\Mail\Password\PasswordResetConfirmedMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_reset_password_with_confirmed_token(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('OldPassword123'),
        ]);

        $token = PasswordResetToken::generateToken();

        $resetToken = new PasswordResetToken;
        $resetToken->email = 'test@example.com';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addHour();
        $resetToken->confirmed_at = now(); // Token is confirmed
        $resetToken->save();

        $response = $this->postJson(route('auth.password.reset'), [
            'email' => 'test@example.com',
            'token' => $token,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password reset successfully',
            ]);

        // Check password was updated
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123', $user->password));

        // Check confirmation email was sent
        Mail::assertQueued(PasswordResetConfirmedMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Check all reset tokens for this email were deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_reset_fails_with_unconfirmed_token(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('OldPassword123'),
        ]);

        $token = PasswordResetToken::generateToken();

        $resetToken = new PasswordResetToken;
        $resetToken->email = 'test@example.com';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addHour();
        $resetToken->confirmed_at = null; // Not confirmed
        $resetToken->save();

        $response = $this->postJson(route('auth.password.reset'), [
            'email' => 'test@example.com',
            'token' => $token,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Reset token must be confirmed first',
            ]);

        // Password should not have changed
        $user->refresh();
        $this->assertTrue(Hash::check('OldPassword123', $user->password));

        Mail::assertNotQueued(PasswordResetConfirmedMail::class);
    }

    public function test_reset_fails_with_expired_token(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('OldPassword123'),
        ]);

        $token = PasswordResetToken::generateToken();

        $resetToken = new PasswordResetToken;
        $resetToken->email = 'test@example.com';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->subMinute(); // Expired
        $resetToken->confirmed_at = now();
        $resetToken->save();

        $response = $this->postJson(route('auth.password.reset'), [
            'email' => 'test@example.com',
            'token' => $token,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Reset token has expired',
            ]);

        // Password should not have changed
        $user->refresh();
        $this->assertTrue(Hash::check('OldPassword123', $user->password));

        Mail::assertNotQueued(PasswordResetConfirmedMail::class);
    }

    public function test_reset_fails_with_password_confirmation_mismatch(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('OldPassword123'),
        ]);

        $token = PasswordResetToken::generateToken();

        $resetToken = new PasswordResetToken;
        $resetToken->email = 'test@example.com';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addHour();
        $resetToken->confirmed_at = now();
        $resetToken->save();

        $response = $this->postJson(route('auth.password.reset'), [
            'email' => 'test@example.com',
            'token' => $token,
            'password' => 'NewPassword123',
            'password_confirmation' => 'DifferentPassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Password should not have changed
        $user->refresh();
        $this->assertTrue(Hash::check('OldPassword123', $user->password));
    }

    public function test_reset_fails_with_weak_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('OldPassword123'),
        ]);

        $token = PasswordResetToken::generateToken();

        $resetToken = new PasswordResetToken;
        $resetToken->email = 'test@example.com';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addHour();
        $resetToken->confirmed_at = now();
        $resetToken->save();

        $response = $this->postJson(route('auth.password.reset'), [
            'email' => 'test@example.com',
            'token' => $token,
            'password' => '123', // Too short
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);

        // Password should not have changed
        $user->refresh();
        $this->assertTrue(Hash::check('OldPassword123', $user->password));
    }

    public function test_reset_with_login()
    {
        $user = User::factory()->create([
            'email' => 'a@a.fr',
            'password' => Hash::make('OldPassword123'),
        ]);
        $token = PasswordResetToken::generateToken();
        $resetToken = new PasswordResetToken;
        $resetToken->email = 'a@a.fr';
        $resetToken->token = $token;
        $resetToken->expires_at = now()->addHour();
        $resetToken->confirmed_at = now();
        $resetToken->save();

        $response = $this->postJson(route('auth.password.reset'), [
            'email' => 'a@a.fr',
            'token' => $token,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        // test login with new password

        $loginResponse = $this->postJson(route('auth.login'), [
            'email' => 'a@a.fr',
            'password' => 'NewPassword123',
            'device_id' => 'test-device-001',
            'device_name' => 'Test Device',
            'remember_me' => false,
        ]);
        $user->refresh();
        $loginResponse->assertStatus(200);
        $this->assertTrue(Hash::check('NewPassword123', $user->password));

    }
}
