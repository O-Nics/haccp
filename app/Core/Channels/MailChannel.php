<?php

namespace App\Core\Channels;

use App\Core\Notifications\BaseNotification;
use App\Features\Notifications\Enums\NotificationChannel;
use App\Features\Notifications\Enums\NotificationSentStatus;
use App\Features\Notifications\Services\NotificationSentService;
use App\Models\User;
use Exception;
use Illuminate\Notifications\Channels\MailChannel as BaseMailChannel;
use Illuminate\Notifications\Notification;
use Symfony\Component\Mailer\Exception\TransportException;

readonly class MailChannel
{
    public function __construct(
        private BaseMailChannel $channel,
        private NotificationSentService $notificationSentService,
    ) {}

    public function send(User $notifiable, BaseNotification|Notification $notification): void
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
}
