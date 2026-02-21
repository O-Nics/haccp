<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use Domain\Business\Data\IndexRoleData;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(private Role $model) {}

    public function indexPaginated(IndexRoleData $indexRoleData, ?array $relation = []): LengthAwarePaginator
    {
        return $this->model::query()
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
