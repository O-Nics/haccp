<?php

declare(strict_types=1);

namespace Tests\Feature\Member;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;

use function Pest\Laravel\getJson;

pest()->group('business');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
});
// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request', function () {
    getJson(route('businesses.roles.index',$this->business))->assertUnauthorized();
});

it('rejects refresh token on businesses index route', function () {
    getJson(route('businesses.roles.index',$this->business), [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});
// --- POLICY ---

it('forbids roles index when user is not a member of the business', function () {
    getJson(route('businesses.roles.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids roles index when member has no roles.view permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    getJson(route('businesses.roles.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('allows roles index when member has roles.view permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_VIEW_ANY);

    getJson(route('businesses.roles.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();
});

// --- VALIDATION ---

it('rejects invalid order value on roles index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_VIEW_ANY);

    getJson(route('businesses.roles.index', $this->business).'?order=invalid', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['order']);
});

it('rejects non-integer per_page on roles index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_VIEW_ANY);

    getJson(route('businesses.roles.index', $this->business).'?per_page=abc', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page']);
});

it('rejects per_page less than 1 on roles index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_VIEW_ANY);

    getJson(route('businesses.roles.index', $this->business).'?per_page=0', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page']);
});

it('rejects page less than 1 on roles index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_VIEW_ANY);

    getJson(route('businesses.roles.index', $this->business).'?page=0', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['page']);
});

// --- BEHAVIOR ---

it('returns 200 with paginated structure on roles index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_VIEW_ANY);

    getJson(route('businesses.roles.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk()
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);
});

it('returns only roles of the requested business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_VIEW_ANY);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $otherRole = Role::factory()->withBusiness($otherBusiness)->create();
    $matchingRole = Role::factory()->withBusiness($this->business)->create();
    $response = getJson(route('businesses.roles.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($matchingRole->id)
        ->and($ids)->not->toContain($otherRole->id);
});

it('respects per_page pagination on roles index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_VIEW_ANY);

    Role::factory()->withBusiness($this->business)->count(5)->create();

    $response = getJson(route('businesses.roles.index', $this->business).'?per_page=2', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($response->json('data'))->toHaveCount(2);
});

it('filters roles by display_name with filter_query', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::ROLES_VIEW_ANY);

        Role::factory()->withBusiness($this->business)->create(['display_name' => 'UniqueRoleName']);

    $response = getJson(route('businesses.roles.index', $this->business).'?filter_query=UniqueRoleName', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    $ids = collect($response->json('data'))->pluck('id');
    $matchingRole = Role::where('business_id', $this->business->id)->where('display_name', 'UniqueRoleName')->first();

    expect($ids)->toContain($matchingRole->id);
});
