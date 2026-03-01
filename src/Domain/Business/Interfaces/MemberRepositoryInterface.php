<?php

declare(strict_types=1);

namespace Domain\Business\Interfaces;

use Domain\Business\Data\IndexMemberData;
use Domain\Business\Data\StoreMemberData;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MemberRepositoryInterface
{
    /**
     * This method retrieves a paginated list of members for a specific business, with optional eager loading of related models.
     *
     * @param  IndexMemberData  $indexMemberData  The data containing pagination and filtering information.
     * @param  Business  $business  The business for which to retrieve members.
     * @param  array|null  $relations  Optional array of related models to eager load.
     * @return LengthAwarePaginator<Member> A paginated list of members.
     */
    public function indexPaginated(IndexMemberData $indexMemberData, Business $business, ?array $relations = []): LengthAwarePaginator;

    /**
     * This method creates a new member for a specific business using the provided data.
     *
     * @param StoreMemberData $storeMemberData The data containing information for creating a new member.
     * @return Member The newly created member instance.
     */
    public function store(StoreMemberData $storeMemberData): Member;

    /**
     * This method deletes a member from the database.
     *
     * @param  Member  $member  The member instance to be deleted.
     */
    public function delete(Member $member): void;
}
