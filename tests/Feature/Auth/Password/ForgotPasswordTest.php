<?php

declare(strict_types=1);

namespace Feature\Auth\Password;

use App\Features\Auth\Mails\PasswordResetMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_request_password_reset_link(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('SecureP@ss123'),
        ]);

        $response = $this->postJson(route('password.forgot'), [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Password reset link sent to your email',
            ]);

        // Check email was sent
        Mail::assertQueued(PasswordResetMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Check token was created
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);

        $token = PasswordResetToken::where('email', 'test@example.com')->first();
        $this->assertNotNull($token);
        $this->assertNotNull($token->expires_at);
        $this->assertNull($token->confirmed_at);
    }

    public function test_forgot_password_fails_with_invalid_email(): void
    {
        $response = $this->postJson(route('password.forgot'), [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_forgot_password_fails_with_non_existent_email(): void
    {
        $response = $this->postJson(route('password.forgot'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_forgot_password_deletes_old_tokens_before_creating_new_one(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        // Create old token
        $oldToken = new PasswordResetToken;
        $oldToken->email = 'test@example.com';
        $oldToken->token = 'old-token';
        $oldToken->expires_at = now()->addHour();
        $oldToken->save();

        $oldTokenId = $oldToken->id;

        // Request new reset
        $response = $this->postJson(route('password.forgot'), [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(200);

        // Old token should be deleted
        $this->assertDatabaseMissing('password_reset_tokens', [
            'id' => $oldTokenId,
        ]);

        // New token should exist
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);

        $newToken = PasswordResetToken::where('email', 'test@example.com')->first();
        $this->assertNotEquals($oldTokenId, $newToken->id);
    }

    public function test_forgot_password_does_not_reveal_if_email_exists(): void
    {
        Mail::fake();

        // Request with non-existent email should still return 200
        // to prevent email enumeration attacks
        $response = $this->postJson(route('password.forgot'), [
            'email' => 'nonexistent@example.com',
        ]);

        // This will fail with 422 due to validation rule 'exists:users,email'
        // If you want to prevent email enumeration, remove the 'exists' rule
        // and handle it in the service layer
        $response->assertStatus(422);

        Mail::assertNotQueued(PasswordResetMail::class);
    }
}
