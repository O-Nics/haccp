<?php

namespace App\Repositories\NotificationSent;

use App\Data\Admin\NotificationSent\IndexNotificationSentRequestData;
use App\Data\StoreNotificationSentData;
use App\Models\NotificationSent;
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
