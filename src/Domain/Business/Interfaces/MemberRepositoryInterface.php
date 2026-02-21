<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Business\Data\IndexMemberData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MemberRepositoryInterface
{
    public function indexPaginated(IndexMemberData $data, ?array $relations = []): LengthAwarePaginator;
}
