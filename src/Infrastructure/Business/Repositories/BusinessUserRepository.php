<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use Domain\Business\Data\IndexBusinessUserData;
use Domain\Business\Interfaces\BusinessUserRepositoryInterface;
use Domain\Business\Models\Business;
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
