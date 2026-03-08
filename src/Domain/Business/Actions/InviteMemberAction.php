<?php

declare(strict_types=1);

namespace Domain\Business\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\BusinessException;
use Domain\Auth\Data\StoreUserData;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Domain\Business\Data\InviteMemberData;
use Domain\Business\Data\MemberInvitedData;
use Domain\Business\Data\StoreMemberData;
use Domain\Business\Ensures\MemberEnsure;
use Domain\Business\Enums\MemberStatusEnum;
use Domain\Business\Events\MemberInvitedEvent;
use Domain\Business\Interfaces\MemberRepositoryInterface;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Illuminate\Support\Str;

final readonly class InviteMemberAction extends BaseAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private MemberRepositoryInterface $memberRepository,
        private MemberEnsure $memberEnsure,
    ) {}

    /**
     * This method handles the invitation of a member to a business. It checks if the user already exists, creates a new user if necessary, ensures that the user has not already been added to the business, creates a new member record, attaches roles to the member, and triggers an event for the invited member.
     *
     * @throws BusinessException
     */
    public function handle(InviteMemberData $data, Business $business): Member
    {
        $user = $this->userRepository->findByEmail($data->email);
        $temporaryPassword = null;

        if (! $user) {
            $temporaryPassword = Str::password();

            $user = $this->userRepository->store(new StoreUserData(
                firstName: $data->firstName,
                lastName: $data->lastName,
                email: $data->email,
                password: $temporaryPassword,
                temporaryPassword: true
            ));
        }

        $this->memberEnsure->hasNotAlreadyBeenAddedToBusiness($user, $business);

        $member = $this->memberRepository->store(
            new StoreMemberData(
                businessId: $business->id,
                userId: $user->id,
                invitedBy: auth()->id(),
                status: MemberStatusEnum::INVITED,
                roleIds: $data->roleIds,
            )
        );

        event(new MemberInvitedEvent(
            new MemberInvitedData(
                member: $member,
                temporaryPassword: $temporaryPassword,
            )
        ));

        return $member;
    }
}
