<?php

declare(strict_types=1);

namespace Domain\Business\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Exceptions\BusinessException;
use Domain\Business\Data\MemberInvitedData;
use Domain\Auth\Data\StoreUserData;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Domain\Business\Data\InviteMemberData;
use Domain\Business\Ensures\MemberEnsure;
use Domain\Business\Enums\MemberStatusEnum;
use Domain\Business\Events\MemberInvitedEvent;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Illuminate\Support\Str;

final readonly class InviteMemberAction extends BaseAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private MemberEnsure $memberEnsure,
    ) {}

    /**
     * @throws BusinessException
     */
    public function handle(InviteMemberData $data, Business $business): Member
    {
        $user = $this->userRepository->findByEmail($data->email);
        $temporaryPassword = null;

        if (! $user) {
            $temporaryPassword = Str::random(12);

            $user = $this->userRepository->store(new StoreUserData(
                firstName: $data->firstName,
                lastName: $data->lastName,
                email: $data->email,
                password: $temporaryPassword,
                temporaryPassword: true
            ));
        }

        $this->memberEnsure->hasNotAlreadyBeenAddedToBusiness($user, $business);

        $member = new Member;
        $member->business_id = $business->id;
        $member->user_id = $user->id;
        $member->invited_by = auth()->id();
        $member->invited_at = now();
        $member->status = MemberStatusEnum::INVITED;
        $member->save();

        event(new MemberInvitedEvent(
            new MemberInvitedData(
                member: $member,
                temporaryPassword: $temporaryPassword,
            )
        ));

        return $member;
    }
}
