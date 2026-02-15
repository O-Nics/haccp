<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Repositories;

use Domain\Notification\Data\IndexNotificationSentRequestData;
use Domain\Notification\Data\StoreNotificationSentData;
use Domain\Notification\Interfaces\NotificationSentRepositoryInterface;
use Domain\Notification\Models\NotificationSent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class NotificationSentRepository implements NotificationSentRepositoryInterface
{
    public function __construct(
        private NotificationSent $model
    ) {}

    public function index(array $relations = []): Collection
    {
        return $this->model->query()
            ->with($relations)
            ->get();
    }

    /**
     * @return LengthAwarePaginator<NotificationSent>
     */
    public function getFilteredPaginated(IndexNotificationSentRequestData $data, array $relations = []): LengthAwarePaginator
    {
        return $this->model->query()
            ->with($relations)
            ->filtered($data->filterQuery, ['recipient', 'name', 'subject'])
            ->when($data->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($data->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->order($data->sort, $data->order)
            ->paginate($data->perPage, ['*'], 'page', $data->page);
    }

    public function store(StoreNotificationSentData $data): NotificationSent
    {
        $notificationSent = new NotificationSent;
        $notificationSent->user_id = $data->userId;
        $notificationSent->recipient = $data->recipient;
        $notificationSent->type = $data->type;
        $notificationSent->name = $data->name;
        $notificationSent->subject = $data->subject;
        $notificationSent->content_html = $data->contentHtml;
        $notificationSent->content_text = $data->contentText;
        $notificationSent->status = $data->status;
        $notificationSent->error_message = $data->errorMessage;
        $notificationSent->save();

        return $notificationSent;
    }
}
