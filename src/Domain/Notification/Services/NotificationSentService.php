<?php

declare(strict_types=1);

namespace Domain\Notification\Services;

use App\Core\Services\BaseService;
use Domain\Auth\Models\User;
use Domain\Notification\Data\IndexNotificationSentRequestData;
use Domain\Notification\Data\StoreNotificationSentData;
use Domain\Notification\Enums\NotificationChannel;
use Domain\Notification\Enums\NotificationSentStatus;
use Domain\Notification\Interfaces\NotificationSentRepositoryInterface;
use Domain\Notification\Models\NotificationSent;
use App\Core\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

readonly class NotificationSentService extends BaseService
{
    public function __construct(
        private NotificationSentRepositoryInterface $notificationSentRepository
    ) {}

    public function index(IndexNotificationSentRequestData $data, bool $isPaginate = false): Collection|LengthAwarePaginator
    {
        if ($isPaginate) {
            return $this->notificationSentRepository->getFilteredPaginated($data);
        }

        return $this->notificationSentRepository->index();
    }

    public function store(
        User $notifiable,
        string $recipient,
        NotificationChannel $type,
        BaseNotification|Notification $notification,
        NotificationSentStatus $status,
        ?string $errorMessage = null,
    ): NotificationSent {
        $subject = $contentHtml = $contentText = null;
        $isConfidential = false;

        if (method_exists($notification, 'toMail')) {
            $mailMessage = $notification->toMail($notifiable);
            assert($mailMessage instanceof MailMessage);
            if ($notification instanceof BaseNotification) {
                $isConfidential = $notification->isConfidential();
            }
            $html = $mailMessage->render()->toHtml();

            $subject = $isConfidential ? 'Confidential' : $mailMessage->subject;

            $contentHtml = $isConfidential ? 'Confidential' : $html;

            $html = preg_replace('#<style.*?>.*?</style>#si', '', $html);
            $contentText = $isConfidential ? 'Confidential' : trim(
                preg_replace('/\n{3,}/', "\n\n",
                    html_entity_decode(
                        strip_tags($html)
                    )
                )
            );
        }

        return $this->notificationSentRepository
            ->store(new StoreNotificationSentData(
                userId: $notifiable->id,
                recipient: $recipient,
                type: $type,
                name: $notification::class,
                subject: $subject,
                contentHtml: $contentHtml,
                contentText: $contentText,
                status: $status,
                errorMessage: $errorMessage,
            ));
    }

    public function resend(NotificationSent $notificationSent): NotificationSent
    {
        $notificationSent->type->model()->resend($notificationSent);

        return $notificationSent;
    }
}
