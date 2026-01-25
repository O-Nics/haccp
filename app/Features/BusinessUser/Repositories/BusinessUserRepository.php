<?php

declare(strict_types=1);

namespace App\Features\BusinessUser\Repositories;

use App\Features\BusinessUser\Data\IndexBusinessUserData;
use App\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class BusinessUserRepository implements BusinessUserRepositoryInterface
{
    public function getPaginated(Business $business, IndexBusinessUserData $indexBusinessUserData, ?array $relations = []): LengthAwarePaginator
    {
        return $business
            ->users()
            ->with($relations)
            ->filtered($indexBusinessUserData->filterQuery, ['first_name', 'last_name', 'email'])
            ->order($indexBusinessUserData->sort, $indexBusinessUserData->order)
            ->paginate(
                perPage: $indexBusinessUserData->perPage,
                page: $indexBusinessUserData->page,
            );
    }
}
