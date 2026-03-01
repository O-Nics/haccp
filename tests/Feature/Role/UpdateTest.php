<?php

declare(strict_types=1);

namespace Tests\Feature\Role;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Permission;
use Domain\Business\Models\Role;

use function Pest\Laravel\patchJson;

pest()->group('business');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
    $this->role = Role::factory()->withBusiness($this->business)->create();
    $this->permission = Permission::factory()->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request on roles update', function () {
    patchJson(route('businesses.roles.update', [$this->business, $this->role]))->assertUnauthorized();
});

it('rejects refresh token on roles update route', function () {
    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids roles update when user is not a member of the business', function () {
    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids roles update when member has no roles.edit permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('passes policy when member has roles.edit permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();
});

// --- VALIDATION ---

it('rejects display_name exceeding max length on roles update', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'display_name' => str_repeat('a', 256),
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['display_name']);
});

it('rejects description exceeding max length on roles update', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'description' => str_repeat('a', 1001),
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['description']);
});

it('rejects non-array permissions on roles update', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'permissions' => 'not-an-array',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['permissions']);
});

it('rejects permissions that do not exist in database on roles update', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'permissions' => [['id' => 'non-existent-id']],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['permissions.0.id']);
});

// --- BEHAVIOR ---

it('returns 200 with role structure on successful update', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'display_name' => 'Nouveau nom',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'display_name', 'description'],
        ]);
});

it('return 409 when trying to update with a role name already existing', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);
    Role::factory()->withBusiness($this->business)->create([
        'name' => 'existing-name',
        'display_name' => 'Existing Name',
    ]);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'display_name' => 'Existing Name',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertConflict();
});

it('updates display_name and name slug when display_name is provided', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'display_name' => 'Responsable Qualité',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    $this->role->refresh();

    expect($this->role->display_name)->toBe('Responsable Qualité')
        ->and($this->role->name)->toBe('responsable-qualite');
});

it('does not update display_name when not provided', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    $originalName = $this->role->name;
    $originalDisplayName = $this->role->display_name;

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'description' => 'Nouvelle description',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    $this->role->refresh();

    expect($this->role->name)->toBe($originalName)
        ->and($this->role->display_name)->toBe($originalDisplayName);
});

it('updates description when provided', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'description' => 'Nouvelle description',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($this->role->refresh()->description)->toBe('Nouvelle description');
});

it('clears description when null is sent', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    $this->role->description = 'Description existante';
    $this->role->save();

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'description' => null,
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($this->role->refresh()->description)->toBeNull();
});

it('does not update description when not provided', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    $this->role->description = 'Description existante';
    $this->role->save();

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'display_name' => 'Nouveau nom',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($this->role->refresh()->description)->toBe('Description existante');
});

it('syncs permissions when permissions are provided', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    $newPermission = Permission::factory()->create();

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'permissions' => [['id' => $newPermission->id]],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($this->role->refresh()->permissions->pluck('id')->toArray())
        ->toBe([$newPermission->id]);
});

it('does not change permissions when permissions are not provided', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    $this->role->permissions()->sync([$this->permission->id]);

    patchJson(route('businesses.roles.update', [$this->business, $this->role]), [
        'display_name' => 'Nouveau nom',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($this->role->refresh()->permissions->pluck('id')->toArray())
        ->toBe([$this->permission->id]);
});

it('returns 404 when role belongs to a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_EDIT);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $roleFromOtherBusiness = Role::factory()->withBusiness($otherBusiness)->create();

    patchJson(route('businesses.roles.update', [$this->business, $roleFromOtherBusiness]), [
        'display_name' => 'Nouveau nom',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNotFound();
});
