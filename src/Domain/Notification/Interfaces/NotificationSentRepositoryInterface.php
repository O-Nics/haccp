<?php

declare(strict_types=1);

namespace Domain\Notification\Interfaces;

use Domain\Notification\Data\IndexNotificationSentRequestData;
use Domain\Notification\Data\StoreNotificationSentData;
use Domain\Notification\Models\NotificationSent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface NotificationSentRepositoryInterface
{
    /**
     * Returns all notification sent records with optional eager-loaded relations.
     *
     * @param  array  $relations  Optional relations to eager load.
     * @return Collection<int, NotificationSent>
     */
    public function index(array $relations = []): Collection;

    /**
     * Returns a filtered and paginated list of notification sent records.
     *
     * @param  IndexNotificationSentRequestData  $data  Pagination, filtering, and sorting parameters.
     * @param  array  $relations  Optional relations to eager load.
     * @return LengthAwarePaginator<NotificationSent>
     */
    public function getFilteredPaginated(IndexNotificationSentRequestData $data, array $relations = []): LengthAwarePaginator;

    /**
     * Persists a new notification sent record and returns the created instance.
     *
     * @param  StoreNotificationSentData  $data  The data used to create the record.
     */
    public function store(StoreNotificationSentData $data): NotificationSent;
}
