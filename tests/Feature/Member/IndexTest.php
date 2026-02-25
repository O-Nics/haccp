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
    getJson(route('businesses.members.index', $this->business))->assertUnauthorized();
});

it('rejects refresh token on businesses index route', function () {
    getJson(route('businesses.members.index', $this->business), [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});
// --- POLICY ---

it('forbids members index when user is not a member of the business', function () {
    getJson(route('businesses.members.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids members index when member has no members.view permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    getJson(route('businesses.members.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('allows members index when member has members.view permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_VIEW_ANY
    );

    getJson(route('businesses.members.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();
});

// --- VALIDATION ---

it('rejects invalid order value on members index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_VIEW_ANY);

    getJson(route('businesses.members.index', $this->business).'?order=invalid', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['order']);
});

it('rejects non-integer per_page on members index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_VIEW_ANY);

    getJson(route('businesses.members.index', $this->business).'?per_page=abc', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page']);
});

it('rejects per_page less than 1 on members index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_VIEW_ANY);

    getJson(route('businesses.members.index', $this->business).'?per_page=0', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page']);
});

it('rejects page less than 1 on members index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_VIEW_ANY);

    getJson(route('businesses.members.index', $this->business).'?page=0', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['page']);
});

// --- BEHAVIOR ---

it('returns 200 with paginated structure on members index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_VIEW_ANY);

    getJson(route('businesses.members.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk()
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);
});

it('returns only members of the requested business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_VIEW_ANY);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $otherMember = Member::factory()
        ->withUser(User::factory()->create())
        ->withBusiness($otherBusiness)
        ->active()
        ->create();

    $response = getJson(route('businesses.members.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    $ids = collect($response->json('data'))->pluck('id');
    $authorizedMember = Member::where('user_id', $this->user->id)->where('business_id', $this->business->id)->first();

    expect($ids)->toContain($authorizedMember->id)
        ->and($ids)->not->toContain($otherMember->id);
});

it('respects per_page pagination on members index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_VIEW_ANY);

    for ($i = 0; $i < 4; $i++) {
        Member::factory()->withBusiness($this->business)->withUser(User::factory()->create())->active()->create();
    }

    $response = getJson(route('businesses.members.index', $this->business).'?per_page=2', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($response->json('data'))->toHaveCount(2)
        ->and($response->json('meta.per_page'))->toBe(2)
        ->and($response->json('meta.total'))->toBe(5);
});

it('filters members by name with filter_query', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_VIEW_ANY);

    $matchingUser = User::factory()->create(['first_name' => 'Alice', 'last_name' => 'Dupont']);
    $otherUser = User::factory()->create(['first_name' => 'Bob', 'last_name' => 'Martin']);

    Member::factory()->withUser($matchingUser)->withBusiness($this->business)->active()->create();
    Member::factory()->withUser($otherUser)->withBusiness($this->business)->active()->create();

    $response = getJson(route('businesses.members.index', $this->business).'?filter_query=Alice', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toHaveCount(1);
});
