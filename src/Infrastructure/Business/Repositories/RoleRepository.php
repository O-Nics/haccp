<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use Domain\Business\Data\IndexRoleData;
use Domain\Business\Data\StoreRoleData;
use Domain\Business\Data\UpdateRoleData;
use Domain\Business\Interfaces\RoleRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\LaravelData\Optional;

final readonly class RoleRepository implements RoleRepositoryInterface
{
    public function __construct(private Role $model) {}

    /**
     * Returns a paginated list of roles for the given business,
     * with optional eager-loaded relations, filtering and sorting.
     */
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

    /**
     * Persists a new role for the given business and returns the created instance.
     */
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

    /**
     * Partially updates a role. Only fields that are not Optional are applied,
     * allowing PATCH semantics where omitted fields are left unchanged.
     * Permissions are synced via pivot table when provided.
     */
    public function update(Role $role, UpdateRoleData $updateRoleData, Business $business): Role
    {

        if (! ($updateRoleData->name instanceof Optional)) {
            $role->name = $updateRoleData->name;
        }

        if (! ($updateRoleData->displayName instanceof Optional)) {
            $role->display_name = $updateRoleData->displayName;
        }

        if (! ($updateRoleData->description instanceof Optional)) {
            $role->description = $updateRoleData->description;
        }

        if (! ($updateRoleData->permissionIds instanceof Optional)) {
            $role->permissions()->sync($updateRoleData->permissionIds);
        }

        $role->save();

        return $role;
    }

    /**
     * Finds a role by its primary key. Returns null if not found.
     */
    public function findById(int|string $roleId): ?Role
    {
        return $this->model::query()->find($roleId);
    }

    /**
     * Hard-deletes the given role from the database.
     */
    public function delete(Role $role): void
    {
        $role->delete();
    }
}
