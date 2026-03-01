<?php

declare(strict_types=1);

namespace Infrastructure\Business\Repositories;

use App\Core\Repositories\BaseRepository;
use Domain\Business\Data\IndexMemberData;
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
     * @param  IndexMemberData  $data  Pagination, filtering, and sorting parameters.
     * @param  Business  $business  The business to list members for.
     * @param  array|null  $relations  Optional relations to eager load.
     * @return LengthAwarePaginator<Member>
     */
    public function indexPaginated(IndexMemberData $data, Business $business, ?array $relations = []): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('business_id', $business->id)
            ->with($relations)
            ->whereHas('user', function ($query) use ($data) {
                if ($data->filterQuery) {
                    $query->where(function ($q) use ($data) {
                        $q->whereLike('first_name', "%{$data->filterQuery}%")
                            ->orWhereLike('last_name', "%{$data->filterQuery}%")
                            ->orWhereLike('email', "%{$data->filterQuery}%");
                    });
                }
            })
            ->orderBy($data->sort ?? 'created_at', $data->order ?? 'asc')
            ->paginate(
                perPage: $data->perPage,
                page: $data->page,
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
}
