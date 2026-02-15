<?php

declare(strict_types=1);

namespace Domain\Auth\Mail;

use App\Core\Mail\BaseMail;
use Domain\Auth\Models\User;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PasswordResetConfirmedMail extends BaseMail
{
    public function __construct(
        public User $user,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset Successful',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.password.confirmed',
            with: [
                'user' => $this->user,
            ],
        );
    }
}
