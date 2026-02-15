<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use Domain\Business\Data\IndexRoleData;
use Domain\Business\Interfaces\BusinessRoleRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class BusinessRoleRepository implements BusinessRoleRepositoryInterface
{
    public function index(IndexRoleData $indexRoleData, ?array $relation = []): LengthAwarePaginator
    {
        return Role::query()
            ->with($relation)
            ->where('business_id', $indexRoleData->businessId)
            ->filtered($indexRoleData->filterQuery, ['display_name', 'description'])
            ->order($indexRoleData->sort, $indexRoleData->order)
            ->paginate(
                perPage: $indexRoleData->perPage,
                page: $indexRoleData->page,
            );
    }
}
