<?php

declare(strict_types=1);

namespace Tests\Unit\Ensure\MemberEnsure;

use App\Core\Exceptions\BusinessException;
use Domain\Auth\Models\User;
use Domain\Business\Ensures\MemberEnsure;
use Domain\Business\Models\Business;
use Illuminate\Database\Eloquent\Relations\HasMany;

pest()->group('unit');

beforeEach(function () {
    $this->memberEnsure = new MemberEnsure();
});

it('throws BusinessException when user is already a member of the business', function () {
    // Data
    $membersQuery = mock(HasMany::class);
    $membersQuery->shouldReceive('where')->with('user_id', 'user-uuid')->andReturnSelf();
    $membersQuery->shouldReceive('exists')->andReturn(true);

    $user = (new User)->setRawAttributes(['id' => 'user-uuid']);

    $business = mock(Business::class);
    $business->shouldReceive('members')->andReturn($membersQuery);

    // Test
    $this->memberEnsure->hasNotAlreadyBeenAddedToBusiness($user, $business);
})->throws(BusinessException::class);

it('throws no exception when user is not yet a member of the business', function () {
    // Data
    $membersQuery = mock(HasMany::class);
    $membersQuery->shouldReceive('where')->with('user_id', 'user-uuid')->andReturnSelf();
    $membersQuery->shouldReceive('exists')->andReturn(false);

    $user = (new User)->setRawAttributes(['id' => 'user-uuid']);

    $business = mock(Business::class);
    $business->shouldReceive('members')->andReturn($membersQuery);

    // Test
    $this->memberEnsure->hasNotAlreadyBeenAddedToBusiness($user, $business);
})->throwsNoExceptions();
