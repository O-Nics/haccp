<?php

declare(strict_types=1);

namespace App\Mail\Password;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

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
