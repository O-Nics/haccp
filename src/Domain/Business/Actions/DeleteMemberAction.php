<?php

declare(strict_types=1);

namespace Domain\Business\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\BusinessException;
use App\Core\Exceptions\NotFoundException;
use Domain\Business\Ensures\MemberEnsure;
use Domain\Business\Interfaces\MemberRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;

final readonly class DeleteMemberAction extends BaseAction
{
    public function __construct(
        private MemberRepositoryInterface $memberRepository,
        private MemberEnsure $memberEnsure,
    ) {}

    /**
     * @throws BusinessException
     * @throws NotFoundException
     */
    public function handle(Member $member, Business $business): void
    {
        $this->memberEnsure->isNotTheOwner($member, $business);
        $this->memberEnsure->isMemberOfBusiness($member, $business);

        $this->memberRepository->delete($member);
    }
}
