<?php

namespace App\Features\Business\Repositories;

use App\Features\Business\Repositories\BusinessRepositoryInterface;
use App\Features\User\Data\IndexBusinessUserData;
use App\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BusinessRepository implements BusinessRepositoryInterface
{
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
