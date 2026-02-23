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
    public function index(array $relations = []): Collection;

    /**
     * @return LengthAwarePaginator<NotificationSent>
     */
    public function getFilteredPaginated(IndexNotificationSentRequestData $data, array $relations = []): LengthAwarePaginator;

    public function store(StoreNotificationSentData $data): NotificationSent;
}
