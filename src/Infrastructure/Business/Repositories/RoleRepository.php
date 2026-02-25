<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use Domain\Business\Data\IndexRoleData;
use Domain\Business\Data\StoreRoleData;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(private Role $model) {}

    public function indexPaginated(IndexRoleData $indexRoleData, Business $business, ?array $relation = []): LengthAwarePaginator
    {
        return $this->model::query()
            ->with($relation)
            ->where('business_id', $business->id)
            ->filtered($indexRoleData->filterQuery, ['display_name', 'description'])
            ->order($indexRoleData->sort, $indexRoleData->order)
            ->paginate(
                perPage: $indexRoleData->perPage,
                page: $indexRoleData->page,
            );
    }

    public function store(StoreRoleData $storeRoleData, Business $business): Role
    {
        $role = $this->model->newInstance();
        $role->business_id = $business->id;
        $role->name = $storeRoleData->name;
        $role->display_name = $storeRoleData->displayName;
        $role->description = $storeRoleData->description;
        $role->save();

        return $role;
    }
}
