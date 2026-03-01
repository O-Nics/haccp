<?php

declare(strict_types=1);

namespace Tests\Feature\Role;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;

use function Pest\Laravel\deleteJson;

pest()->group('business');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
    $this->role = Role::factory()->withBusiness($this->business)->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request on roles delete', function () {
    deleteJson(route('businesses.roles.delete', $this->business))->assertUnauthorized();
});

it('rejects refresh token on roles delete route', function () {
    deleteJson(route('businesses.roles.delete', $this->business), [], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids roles delete when user is not a member of the business', function () {
    deleteJson(route('businesses.roles.delete', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids roles delete when member has no members.remove permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    deleteJson(route('businesses.roles.delete', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('passes policy when member has members.remove permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.roles.delete', $this->business), [
        'role_ids' => [$this->role->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();
});

// --- VALIDATION ---

it('requires role_ids on roles delete', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.roles.delete', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['role_ids']);
});

it('rejects role_ids when not an array on roles delete', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.roles.delete', $this->business), [
        'role_ids' => 'not-an-array',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['role_ids']);
});

// --- BEHAVIOR ---

it('deletes a role and returns 204', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.roles.delete', $this->business), [
        'role_ids' => [$this->role->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Role::find($this->role->id))->toBeNull();
});

it('deletes multiple roles in a single request', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    $secondRole = Role::factory()->withBusiness($this->business)->create();

    deleteJson(route('businesses.roles.delete', $this->business), [
        'role_ids' => [$this->role->id, $secondRole->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Role::find($this->role->id))->toBeNull()
        ->and(Role::find($secondRole->id))->toBeNull();
});

it('returns 404 when a role belongs to a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $roleFromOtherBusiness = Role::factory()->withBusiness($otherBusiness)->create();

    deleteJson(route('businesses.roles.delete', $this->business), [
        'role_ids' => [$roleFromOtherBusiness->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNotFound();
});

it('does not delete any role when one role belongs to a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $roleFromOtherBusiness = Role::factory()->withBusiness($otherBusiness)->create();

    deleteJson(route('businesses.roles.delete', $this->business), [
        'role_ids' => [$this->role->id, $roleFromOtherBusiness->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNotFound();

    // Two-pass: validation runs before any deletion — this->role must still exist
    expect(Role::find($this->role->id))->not->toBeNull();
});
