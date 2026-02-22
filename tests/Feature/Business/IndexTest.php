<?php

declare(strict_types=1);

namespace Tests\Feature\Business;

use Domain\Auth\Models\User;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;

use function Pest\Laravel\getJson;

pest()->group('business');

beforeEach(function () {
    $this->user = User::factory()->create();
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

// --- VALIDATION ---

it('rejects invalid order value', function () {
    getJson(route('businesses.index') . '?order=invalid', [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['order']);
});

it('rejects non-integer per_page', function () {
    getJson(route('businesses.index') . '?per_page=abc', [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page']);
});

it('rejects per_page less than 1', function () {
    getJson(route('businesses.index') . '?per_page=0', [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page']);
});

it('rejects page less than 1', function () {
    getJson(route('businesses.index') . '?page=0', [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['page']);
});

// --- BEHAVIOR ---

it('returns 200 with paginated structure', function () {
    getJson(route('businesses.index'), [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertOk()
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta'  => ['current_page', 'per_page', 'total'],
        ]);
});

it('returns only businesses the user is a member of', function () {
    $ownBusiness = Business::factory()->withBusinessType()->withOrganisation()->create();
    Member::factory()->withUser($this->user)->withBusiness($ownBusiness)->active()->create();

    $otherBusiness = Business::factory()->withBusinessType()->withOrganisation()->create();

    $response = getJson(route('businesses.index'), [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($ownBusiness->id)
        ->and($ids)->not->toContain($otherBusiness->id);
});

it('returns correct structure for each business', function () {
    $business = Business::factory()->withBusinessType()->withOrganisation()->create();
    Member::factory()->withUser($this->user)->withBusiness($business)->active()->create();

    getJson(route('businesses.index'), [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name','members'],
            ],
        ]);
});

it('returns only user member', function () {
    $business = Business::factory()->withBusinessType()->withOrganisation()->create();
    Member::factory()->withUser($this->user)->withBusiness($business)->active()->create();
    Member::factory()->withUser(User::factory()->create())->withBusiness($business)->active()->create();

    getJson(route('businesses.index'), [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertOk()
        ->assertJsonCount(1, 'data.0.members')
        ->assertJsonPath('data.0.members.0.user.id', $this->user->id);
});

it('returns empty list when user has no memberships', function () {
    Business::factory()->withBusinessType()->withOrganisation()->create();

    $response = getJson(route('businesses.index'), [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertOk();

    expect($response->json('data'))->toBeEmpty();
});

it('filters businesses by name with filter_query', function () {
    $matching = Business::factory()->withBusinessType()->withOrganisation()->create(['name' => 'Pizza Palace']);
    $other    = Business::factory()->withBusinessType()->withOrganisation()->create(['name' => 'Burger Barn']);

    Member::factory()->withUser($this->user)->withBusiness($matching)->active()->create();
    Member::factory()->withUser($this->user)->withBusiness($other)->active()->create();

    $response = getJson(route('businesses.index') . '?filter_query=Pizza', [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($matching->id)
        ->and($ids)->not->toContain($other->id);
});

it('respects per_page pagination', function () {
    $businesses = Business::factory()->withBusinessType()->withOrganisation()->count(5)->create();

    foreach ($businesses as $business) {
        Member::factory()->withUser($this->user)->withBusiness($business)->active()->create();
    }

    $response = getJson(route('businesses.index') . '?per_page=2', [
        'Authorization' => 'Bearer ' . accessToken($this->user),
    ])->assertOk();

    expect($response->json('data'))->toHaveCount(2)
        ->and($response->json('meta.per_page'))->toBe(2)
        ->and($response->json('meta.total'))->toBe(5);
});
