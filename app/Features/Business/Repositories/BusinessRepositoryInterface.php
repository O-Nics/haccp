<?php

namespace App\Features\Business\Repositories;

use App\Features\User\Data\IndexBusinessUserData;
use App\Models\Business;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BusinessRepositoryInterface
{
    public function getBusinessUsersPaginated(Business $business, IndexBusinessUserData $indexBusinessUserData, ?array $relation = []) : LengthAwarePaginator;

}
