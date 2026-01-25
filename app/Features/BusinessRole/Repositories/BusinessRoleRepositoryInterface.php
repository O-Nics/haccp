<?php

namespace App\Features\BusinessRole\Repositories;

use App\Features\BusinessRole\Data\IndexBusinessRoleData;
use App\Models\Business;
use Illuminate\Pagination\LengthAwarePaginator;

interface BusinessRoleRepositoryInterface
{
    public function index(Business $business, IndexBusinessRoleData $indexRoleData, ?array $relation = []): LengthAwarePaginator;
}
