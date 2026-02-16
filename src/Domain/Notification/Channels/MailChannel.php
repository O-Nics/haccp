<?php

declare(strict_types=1);

namespace Domain\Notification\Channels;

use App\Core\Notifications\BaseNotification;
use Domain\Auth\Models\User;
use Domain\Notification\Enums\NotificationChannel;
use Domain\Notification\Enums\NotificationSentStatus;
use Domain\Notification\Services\NotificationSentService;
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
        if (
            ! method_exists($notification, 'toMail')
            || ($notification instanceof BaseNotification && ! $notification->mustSend($notifiable))
        ) {
            return;
        }

        $email = $notifiable->routeNotificationFor('mail', $notification);
        $mailer = config('mail.default');

        if (! $mailer || in_array($mailer, ['log', 'array'], true)) {
            $this->notificationSentService->store(
                notifiable: $notifiable,
                recipient: $email,
                type: NotificationChannel::Email,
                notification: $notification,
                status: NotificationSentStatus::Skipped,
                errorMessage: 'Mailer disabled',
            );

            return;
        }

        $errorMessage = null;

        try {
            $this->channel->send($notifiable, $notification);
            $status = NotificationSentStatus::Sent;
        } catch (TransportException $e) {
            report($e);

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

        $this->notificationSentService->store(
            notifiable: $notifiable,
            recipient: $email,
            type: NotificationChannel::Email,
            notification: $notification,
            status: $status,
            errorMessage: $errorMessage,
        );
    }
}
