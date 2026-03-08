<?php

declare(strict_types=1);

namespace Infrastructure\Notification\Repositories;

use App\Core\Repositories\BaseRepository;
use Domain\Notification\Data\IndexNotificationSentRequestData;
use Domain\Notification\Data\StoreNotificationSentData;
use Domain\Notification\Interfaces\NotificationSentRepositoryInterface;
use Domain\Notification\Models\NotificationSent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class NotificationSentRepository extends BaseRepository implements NotificationSentRepositoryInterface
{
    public function __construct(
        private NotificationSent $model
    ) {}

    /**
     * Returns all notification sent records with optional eager-loaded relations.
     *
     * @param  array  $relations  Optional relations to eager load.
     * @return Collection<int, NotificationSent>
     */
    public function index(array $relations = []): Collection
    {
        return $this->model->query()
            ->with($relations)
            ->get();
    }

    /**
     * Returns a filtered and paginated list of notification sent records.
     *
     * @param  IndexNotificationSentRequestData  $data  Pagination, filtering, and sorting parameters.
     * @param  array  $relations  Optional relations to eager load.
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

    /**
     * Persists a new notification sent record and returns the created instance.
     *
     * @param  StoreNotificationSentData  $data  The data used to create the record.
     */
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
