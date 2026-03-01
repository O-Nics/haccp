<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use App\Core\Repositories\BaseRepository;
use Domain\Business\Data\IndexMemberData;
use Domain\Business\Data\StoreMemberData;
use Domain\Business\Interfaces\MemberRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final readonly class MemberRepository extends BaseRepository implements MemberRepositoryInterface
{
    public function __construct(private Member $model) {}

    /**
     * Returns a paginated list of members for the given business,
     * with optional eager-loaded relations and full-name/email filtering.
     *
     * @param  IndexMemberData  $indexMemberData  Pagination, filtering, and sorting parameters.
     * @param  Business  $business  The business to list members for.
     * @param  array|null  $relations  Optional relations to eager load.
     * @return LengthAwarePaginator<Member>
     */
    public function indexPaginated(IndexMemberData $indexMemberData, Business $business, ?array $relations = []): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('business_id', $business->id)
            ->with($relations)
            ->whereHas('user', function ($query) use ($indexMemberData) {
                if ($indexMemberData->filterQuery) {
                    $query->where(function ($q) use ($indexMemberData) {
                        $q->whereLike('first_name', "%{$indexMemberData->filterQuery}%")
                            ->orWhereLike('last_name', "%{$indexMemberData->filterQuery}%")
                            ->orWhereLike('email', "%{$indexMemberData->filterQuery}%");
                    });
                }
            })
            ->orderBy($indexMemberData->sort ?? 'created_at', $indexMemberData->order ?? 'asc')
            ->paginate(
                perPage: $indexMemberData->perPage,
                page: $indexMemberData->page,
            );
    }

    /**
     * Soft-deletes the given member record.
     *
     * @param  Member  $member  The member to delete.
     */
    public function delete(Member $member): void
    {
        $member->delete();
    }

    public function store(StoreMemberData $storeMemberData): Member
    {
        $member = $this->model->newInstance();
        $member->business_id = $storeMemberData->businessId;
        $member->user_id = $storeMemberData->userId;
        $member->invited_by = $storeMemberData->invitedBy;
        $member->status = $storeMemberData->status;
        $member->save();

        if ($storeMemberData->roleIds) {
            $member->roles()->attach($storeMemberData->roleIds);
        }

        return $member;
    }
}
