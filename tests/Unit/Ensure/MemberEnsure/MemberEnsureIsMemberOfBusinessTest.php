<?php

declare(strict_types=1);

namespace Tests\Unit\Ensure\MemberEnsure;

use App\Core\Exceptions\NotFoundException;
use Domain\Business\Ensures\MemberEnsure;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Illuminate\Database\Eloquent\Relations\HasMany;

pest()->group('unit');

beforeEach(function () {
    $this->memberEnsure = new MemberEnsure();
});

it('throws NotFoundException when the member does not belong to the business', function () {
    // Data
    $membersQuery = mock(HasMany::class);
    $membersQuery->shouldReceive('where')->with('id', 'member-uuid')->andReturnSelf();
    $membersQuery->shouldReceive('exists')->andReturn(false);

    $member = (new Member)->setRawAttributes(['id' => 'member-uuid']);

    $business = mock(Business::class);
    $business->shouldReceive('members')->andReturn($membersQuery);

    // Test
    $this->memberEnsure->isMemberOfBusiness($member, $business);
})->throws(NotFoundException::class);

it('throws no exception when the member belongs to the business', function () {
    // Data
    $membersQuery = mock(HasMany::class);
    $membersQuery->shouldReceive('where')->with('id', 'member-uuid')->andReturnSelf();
    $membersQuery->shouldReceive('exists')->andReturn(true);

    $member = (new Member)->setRawAttributes(['id' => 'member-uuid']);

    $business = mock(Business::class);
    $business->shouldReceive('members')->andReturn($membersQuery);

    // Test
    $this->memberEnsure->isMemberOfBusiness($member, $business);
})->throwsNoExceptions();
