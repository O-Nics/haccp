<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Business\Data\IndexRoleData;
use Domain\Business\Data\StoreRoleData;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function indexPaginated(IndexRoleData $indexRoleData, Business $business, ?array $relation = []): LengthAwarePaginator;

    public function store(StoreRoleData $storeRoleData, Business $business): Role;
}
