<?php

declare(strict_types=1);

namespace Tests\Feature\Role;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Permission;
use Domain\Business\Models\Role;

use function Pest\Laravel\postJson;

pest()->group('business');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
    $this->permission = Permission::factory()->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request on roles store', function () {
    postJson(route('businesses.roles.store', $this->business))->assertUnauthorized();
});

it('rejects refresh token on roles store route', function () {
    postJson(route('businesses.roles.store', $this->business), [], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids roles store when user is not a member of the business', function () {
    postJson(route('businesses.roles.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids roles store when member has no roles.create permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    postJson(route('businesses.roles.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('passes policy when member has roles.create permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    // Policy passée → validation échoue (pas de body) → 422, pas 403
    postJson(route('businesses.roles.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable();
});

// --- VALIDATION ---

it('requires display_name and permissions on roles store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    postJson(route('businesses.roles.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['display_name', 'permissions']);
});

it('rejects display_name exceeding max length on roles store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => str_repeat('a', 256),
        'permissions' => [['id' => $this->permission->id]],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['display_name']);
});

it('rejects description exceeding max length on roles store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => 'Responsable qualité',
        'description' => str_repeat('a', 1001),
        'permissions' => [['id' => $this->permission->id]],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['description']);
});

it('rejects permissions that do not exist in the database on roles store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => 'Responsable qualité',
        'permissions' => [['id' => 'non-existent-id']],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['permissions.0.id']);
});

it('rejects non-array permissions on roles store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => 'Responsable qualité',
        'permissions' => 'not-an-array',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['permissions']);
});

// --- BEHAVIOR ---

it('creates a role with the correct data in database', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => 'Responsable Qualité',
        'description' => 'Gère les contrôles qualité',
        'permissions' => [['id' => $this->permission->id]],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    expect(Role::where('business_id', $this->business->id)
        ->where('name', 'responsable-qualite')
        ->where('display_name', 'Responsable Qualité')
        ->where('description', 'Gère les contrôles qualité')
        ->exists()
    )->toBeTrue();
});

it('stores role name as slug of display_name', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => 'Étoile du Nord',
        'permissions' => [['id' => $this->permission->id]],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    expect(Role::where('business_id', $this->business->id)
        ->where('name', 'etoile-du-nord')
        ->exists()
    )->toBeTrue();
});

it('creates role without description when description is omitted', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => 'Responsable qualité',
        'permissions' => [['id' => $this->permission->id]],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    expect(Role::where('business_id', $this->business->id)
        ->whereNull('description')
        ->exists()
    )->toBeTrue();
});

it('returns 201 with role structure on successful store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => 'Responsable qualité',
        'description' => 'Gère les contrôles qualité',
        'permissions' => [['id' => $this->permission->id]],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'name', 'display_name', 'description'],
        ]);
});

it('returns 409 when role with same name already exists in the business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    // La factory crée name = 'word', display_name = 'Word', to_slug('Word') = 'word' → conflit garanti
    $existingRole = Role::factory()->withBusiness($this->business)->create();

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => $existingRole->display_name,
        'permissions' => [['id' => $this->permission->id]],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertConflict();
});

it('allows creating a role with the same name in a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_CREATE);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $existingRole = Role::factory()->withBusiness($otherBusiness)->create();

    postJson(route('businesses.roles.store', $this->business), [
        'display_name' => $existingRole->display_name,
        'permissions' => [['id' => $this->permission->id]],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();
});
