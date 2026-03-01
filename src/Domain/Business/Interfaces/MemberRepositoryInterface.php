<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Business\Data\IndexMemberData;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MemberRepositoryInterface
{
    /**
     * This method retrieves a paginated list of members for a specific business, with optional eager loading of related models.
     *
     * @param  IndexMemberData  $data  The data containing pagination and filtering information.
     * @param  Business  $business  The business for which to retrieve members.
     * @param  array|null  $relations  Optional array of related models to eager load.
     * @return LengthAwarePaginator<Member> A paginated list of members.
     */
    public function indexPaginated(IndexMemberData $data, Business $business, ?array $relations = []): LengthAwarePaginator;

    /**
     * This method deletes a member from the database.
     *
     * @param  Member  $member  The member instance to be deleted.
     */
    public function delete(Member $member): void;
}
