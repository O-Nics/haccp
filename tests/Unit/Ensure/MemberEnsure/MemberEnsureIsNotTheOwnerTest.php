<?php

declare(strict_types=1);

namespace Tests\Unit\Ensure\MemberEnsure;

use App\Core\Exceptions\BusinessException;
use Domain\Business\Ensures\MemberEnsure;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Organisation;

pest()->group('unit');

beforeEach(function () {
    $this->memberEnsure = new MemberEnsure();
});

it('throws BusinessException when the member is the owner of the business', function () {
    // Data
    $organisation = (new Organisation)->setRawAttributes(['owner_id' => 'user-uuid']);
    $business = (new Business)->setRawAttributes(['id' => 'business-uuid']);
    $business->setRelation('organisation', $organisation);

    $member = (new Member)->setRawAttributes(['user_id' => 'user-uuid', 'business_id' => 'business-uuid']);
    $member->setRelation('business', $business);

    // Test
    $this->memberEnsure->isNotTheOwner($member, $business);
})->throws(BusinessException::class);

it('throws no exception when the member is not the owner of the business', function () {
    // Data
    $organisation = (new Organisation)->setRawAttributes(['owner_id' => 'owner-uuid']);
    $business = (new Business)->setRawAttributes(['id' => 'business-uuid']);
    $business->setRelation('organisation', $organisation);

    $member = (new Member)->setRawAttributes(['user_id' => 'user-uuid', 'business_id' => 'business-uuid']);
    $member->setRelation('business', $business);

    // Test
    $this->memberEnsure->isNotTheOwner($member, $business);
})->throwsNoExceptions();
