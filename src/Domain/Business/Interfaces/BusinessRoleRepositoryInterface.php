<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Business\Data\IndexBusinessRoleData;
use Domain\Business\Models\Business;
use Illuminate\Pagination\LengthAwarePaginator;

interface BusinessRoleRepositoryInterface
{
    public function index(Business $business, IndexBusinessRoleData $indexRoleData, ?array $relation = []): LengthAwarePaginator;
}
