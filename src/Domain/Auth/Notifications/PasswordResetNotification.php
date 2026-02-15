<?php

declare(strict_types=1);

namespace Domain\Auth\Notifications;

use Domain\Auth\Models\User;
use Domain\Notification\Mails\UserMailNotification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetNotification extends UserMailNotification
{
    public function __construct(
        private readonly string $token,
    ) {}

    protected function mailMessage(User $user, MailMessage $message): MailMessage
    {
        $resetUrl = config('app.frontend_url').'/reset-password?token='.$this->token.'&email='.urlencode($user->email);

        return $message
            ->subject('Password Reset Request')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $resetUrl)
            ->line('This link will expire in '.config('auth.password_reset.expiration', 60).' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
