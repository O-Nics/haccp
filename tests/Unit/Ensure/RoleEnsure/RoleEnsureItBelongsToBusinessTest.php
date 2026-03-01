<?php

declare(strict_types=1);

namespace Tests\Unit\Ensure\RoleEnsure;

use App\Core\Exceptions\NotFoundException;
use Domain\Business\Ensures\RoleEnsure;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;

pest()->group('unit');

beforeEach(function () {
    $this->roleEnsure = new RoleEnsure();
});

it('throws NotFoundException when the role belongs to a different business', function () {
    // Data
    $role = (new Role)->setRawAttributes(['business_id' => 'business-uuid-A']);
    $business = (new Business)->setRawAttributes(['id' => 'business-uuid-B']);

    // Test
    $this->roleEnsure->itBelongsToBusiness($role, $business);
})->throws(NotFoundException::class);

it('throws no exception when the role belongs to the given business', function () {
    // Data
    $role = (new Role)->setRawAttributes(['business_id' => 'business-uuid-A']);
    $business = (new Business)->setRawAttributes(['id' => 'business-uuid-A']);

    // Test
    $this->roleEnsure->itBelongsToBusiness($role, $business);
})->throwsNoExceptions();

it('throws NotFoundException with a custom message when provided', function () {
    // Data
    $role = (new Role)->setRawAttributes(['business_id' => 'business-uuid-A']);
    $business = (new Business)->setRawAttributes(['id' => 'business-uuid-B']);

    // Test
    $this->roleEnsure->itBelongsToBusiness($role, $business, 'Custom error message.');
})->throws(NotFoundException::class, 'Custom error message.');
