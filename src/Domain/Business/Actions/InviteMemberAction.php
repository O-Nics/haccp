<?php

declare(strict_types=1);

namespace Domain\Business\Actions;

use App\Core\Actions\BaseAction;
use Domain\Auth\Data\StoreUserData;
use Domain\Auth\Interfaces\UserRepositoryInterface;
use Domain\Business\Data\InviteMemberData;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Illuminate\Support\Str;

final readonly class InviteMemberAction extends BaseAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function handle(InviteMemberData $data, Business $business): Member
    {
        $user = $this->userRepository->findByEmail($data->email);

        if (! $user) {
            $user = $this->userRepository->store(new StoreUserData(
                firstName: $data->firstName,
                lastName: $data->lastName,
                email: $data->email,
                password: Str::random(12),
            ));
        }

        $existing = Member::query()
            ->where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $member = new Member;
        $member->business_id = $data->businessId;
        $member->user_id = $user->id;
        $member->invited_by = auth()->id();
        $member->invited_at = now();
        $member->status = 'invited';
        $member->save();

        return $member;
    }
}
