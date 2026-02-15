<?php

declare(strict_types=1);

namespace Domain\Auth\Mail;

use App\Core\Mail\BaseMail;
use Domain\Auth\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PasswordResetMail extends BaseMail
{
    public function __construct(
        public User $user,
        public string $token,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset Request',
        );
    }

    public function content(): Content
    {
        $resetUrl = config('app.frontend_url').'/reset-password?token='.$this->token.'&email='.urlencode($this->user->email);

        return new Content(
            markdown: 'emails.password.reset',
            with: [
                'user' => $this->user,
                'resetUrl' => $resetUrl,
                'token' => $this->token,
                'expirationMinutes' => config('auth.password_reset.expiration', 60),
            ],
        );
    }
}
