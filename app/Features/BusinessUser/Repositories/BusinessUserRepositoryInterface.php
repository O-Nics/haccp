<?php

declare(strict_types=1);

namespace App\Features\BusinessUser\Repositories;

use App\Features\BusinessUser\Data\IndexBusinessUserData;
use App\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BusinessUserRepositoryInterface
{
    public function getPaginated(Business $business, IndexBusinessUserData $indexBusinessUserData, ?array $relations = []): LengthAwarePaginator;
}
