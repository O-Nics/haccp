<?php

namespace App\Core\Mail;

use App\Enums\NotificationChannel;
use App\Enums\NotificationSentStatus;
use App\Models\EmailValidation;
use App\Models\ProClient;
use App\Models\User;
use App\Notifications\Generic\BaseNotification;
use App\Services\EmailValidation\EmailValidationApiInterface;
use App\Services\NotificationSentService;
use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Channels\MailChannel as BaseMailChannel;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mailer\Exception\TransportException;

class MailChannel
{
    public function __construct(
        private BaseMailChannel $channel,
        private EmailValidationApiInterface $abstractApi,
        private NotificationSentService $notificationSentService,
    ) {}

    public function send(User|ProClient|AnonymousNotifiable $notifiable, BaseNotification|Notification $notification): void
    {
        // Check if the notification should be sent
        if (
            ! method_exists($notification, 'toMail')
            || ($notification instanceof BaseNotification && ! $notification->mustSend($notifiable))
        ) {
            return;
        }

        // Send email
        $email = $notifiable->routeNotificationFor('mail', $notification);
        $errorMessage = null;

        if (! $this->canSendEmail($email)) {
            // Check if the notification should be sent
            $status = NotificationSentStatus::Blocked;
        } else {
            try {
                $this->channel->send($notifiable, $notification);
                $status = NotificationSentStatus::Sent;
            } catch (TransportException $transportException) {
                // Retry once
                try {
                    $this->channel->send($notifiable, $notification);
                    $status = NotificationSentStatus::Sent;
                } catch (Exception $exception) {
                    report($exception);
                    $status = NotificationSentStatus::Error;
                    $errorMessage = $exception->getMessage();
                }
            } catch (Exception $exception) {
                report($exception);
                $status = NotificationSentStatus::Error;
                $errorMessage = $exception->getMessage();
            }
        }

        $this->notificationSentService
            ->store(
                notifiable: $notifiable,
                recipient: $email,
                type: NotificationChannel::Email,
                notification: $notification,
                status: $status,
                errorMessage: $errorMessage,
            );
    }

    public function canSendEmail(string $email): bool
    {
        $validation = EmailValidation::query()->where('email', $email)->first();
        if ($validation) {
            return $validation->is_valid;
        }

        $validation = new EmailValidation;
        $validation->email = $email;
        $validation->last_abstract_api_return = $this->abstractApi->checkEmail($email);
        $validation->is_valid = $validation->last_abstract_api_return->deliverability === 'DELIVERABLE';
        $validation->last_checked_at = now();
        $validation->save();

        return $validation->is_valid;
    }
}
