<?php

declare(strict_types=1);

namespace Tests\Unit\Ensure\RoleEnsure;

use App\Core\Exceptions\BusinessException;
use Domain\Business\Ensures\RoleEnsure;
use Domain\Business\Models\Business;
use Illuminate\Database\Eloquent\Relations\HasMany;

pest()->group('unit');

beforeEach(function () {
    $this->roleEnsure = new RoleEnsure;
});

it('throws BusinessException when a role with the same name already exists in the business', function () {
    // Data
    $rolesQuery = mock(HasMany::class);
    $rolesQuery->shouldReceive('where')->with('name', 'existing-role')->andReturnSelf();
    $rolesQuery->shouldReceive('exists')->andReturn(true);

    $business = mock(Business::class);
    $business->shouldReceive('roles')->andReturn($rolesQuery);

    // Test
    $this->roleEnsure->itDoesNotExist('existing-role', $business);
})->throws(BusinessException::class);

it('throws no exception when no role with the same name exists in the business', function () {
    // Data
    $rolesQuery = mock(HasMany::class);
    $rolesQuery->shouldReceive('where')->with('name', 'new-role')->andReturnSelf();
    $rolesQuery->shouldReceive('exists')->andReturn(false);

    $business = mock(Business::class);
    $business->shouldReceive('roles')->andReturn($rolesQuery);

    // Test
    $this->roleEnsure->itDoesNotExist('new-role', $business);
})->throwsNoExceptions();
