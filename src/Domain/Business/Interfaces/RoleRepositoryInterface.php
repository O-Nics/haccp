<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Business\Data\IndexRoleData;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function index(IndexRoleData $indexRoleData, ?array $relation = []): LengthAwarePaginator;
}
