<?php

namespace App\Features\Business\Repositories;

use App\Core\Repositories\BaseRepository;
use App\Features\User\Data\IndexBusinessUserData;
use App\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BusinessRepository extends BaseRepository implements BusinessRepositoryInterface
{
    public function __construct()
    {
        $this->model = Business::class;
    }

    public function getBusinessUsersPaginated(Business $business, IndexBusinessUserData $indexBusinessUserData, ?array $relation = []): LengthAwarePaginator
    {
        return $business
            ->users()
            ->with($relation)
            ->filtered($indexBusinessUserData->filterQuery, ['name'])
            ->order($indexBusinessUserData->sort, $indexBusinessUserData->order)
            ->paginate(
                perPage: $indexBusinessUserData->perPage,
                page: $indexBusinessUserData->page,
            );
    }
}
