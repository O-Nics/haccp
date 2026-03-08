<?php

declare(strict_types=1);

namespace Domain\Business\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use Domain\Business\Data\DeleteMemberData;
use Domain\Business\Ensures\MemberEnsure;
use Domain\Business\Interfaces\MemberRepositoryInterface;
use Domain\Business\Models\Business;

final readonly class DeleteMemberAction extends BaseAction
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
        private MemberEnsure              $memberEnsure,
    ) {}

    /**
     * Bulk-deletes members for the given business.
     * Validates all IDs belong to the business and none is the owner, then bulk soft-deletes.
     *
     * @throws NotFoundException 404 if any member does not belong to the business
     * @throws BusinessException 409 if any member is the organisation owner
     */
    public function handle(DeleteMemberData $deleteMemberData, Business $business): void
    {
        $members = $this->memberRepository->findByIdsForBusiness($deleteMemberData->memberIds, $business);

        foreach ($members as $member) {
            $this->memberEnsure->isNotThemselves($member, auth()->user());
        }
        $this->memberEnsure->areAllMembersInBusiness(
            $members,
            $deleteMemberData->memberIds,
        );

        $this->memberEnsure->noneAreOwner($members, $business);

        $this->memberRepository->deleteMany($members);
    }
}
