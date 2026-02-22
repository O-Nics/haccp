<?php

declare(strict_types=1);

namespace Tests\Feature\Member;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Permission;
use Domain\Business\Models\Role;

use function Pest\Laravel\getJson;

pest()->group('business');

beforeEach(function () {
    $this->user     = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
});
// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request', function () {
    getJson(route('businesses.index'))->assertUnauthorized();
});

it('rejects refresh token on businesses index route', function () {
    getJson(route('businesses.index'), [
        'Authorization' => 'Bearer ' . refreshToken($this->user),
    ])->assertForbidden();
});
// --- POLICY ---

it('forbids members index when user is not a member of the business', function () {
    getJson(route('businesses.members.index', $this->business), [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertForbidden();
});

it('forbids members index when member has no members.view permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    getJson(route('businesses.members.index', $this->business), [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertForbidden();
});

it('allows members index when member has members.view permission', function () {
    $permission = Permission::factory()->create([
        'can'            => PermissionEnum::MEMBERS_VIEW->value,
        'category'       => 'members',
        'category_label' => 'Membres',
        'display_name'   => 'Voir les membres',
    ]);

    $role = Role::factory()->withBusiness($this->business)->create();
    $role->permissions()->attach($permission);

    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($role)->active()->create();

    getJson(route('businesses.members.index', $this->business), [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertOk();
});
