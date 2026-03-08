<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;
use Domain\Product\Models\Product;

use function Pest\Laravel\getJson;

pest()->group('product');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request on products index', function () {
    getJson(route('businesses.products.index', $this->business))->assertUnauthorized();
});

it('rejects refresh token on products index route', function () {
    getJson(route('businesses.products.index', $this->business), [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids products index when user is not a member of the business', function () {
    getJson(route('businesses.products.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids products index when member has no product.view-any permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    getJson(route('businesses.products.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('allows products index when member has product.view-any permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_VIEW_ANY);

    getJson(route('businesses.products.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();
});

// --- VALIDATION ---

it('rejects invalid order value on products index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_VIEW_ANY);

    getJson(route('businesses.products.index', $this->business).'?order=invalid', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['order']);
});

it('rejects non-integer per_page on products index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_VIEW_ANY);

    getJson(route('businesses.products.index', $this->business).'?per_page=abc', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page']);
});

it('rejects per_page less than 1 on products index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_VIEW_ANY);

    getJson(route('businesses.products.index', $this->business).'?per_page=0', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page']);
});

it('rejects page less than 1 on products index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_VIEW_ANY);

    getJson(route('businesses.products.index', $this->business).'?page=0', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['page']);
});

// --- BEHAVIOR ---

it('returns 200 with paginated structure on products index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_VIEW_ANY);

    getJson(route('businesses.products.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk()
        ->assertJsonStructure([
            'data',
            'links' => ['first', 'last', 'prev', 'next'],
            'meta' => ['current_page', 'per_page', 'total'],
        ]);
});

it('returns only products of the requested business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_VIEW_ANY);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $otherProduct = Product::factory()->withBusiness($otherBusiness)->create();
    $matchingProduct = Product::factory()->withBusiness($this->business)->create();

    $response = getJson(route('businesses.products.index', $this->business), [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    $ids = collect($response->json('data'))->pluck('id');

    expect($ids)->toContain($matchingProduct->id)
        ->and($ids)->not->toContain($otherProduct->id);
});

it('respects per_page pagination on products index', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_VIEW_ANY);

    Product::factory()->withBusiness($this->business)->count(5)->create();

    $response = getJson(route('businesses.products.index', $this->business).'?per_page=2', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($response->json('data'))->toHaveCount(2);
});

it('filters products by name with filter_query', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_VIEW_ANY);

    Product::factory()->withBusiness($this->business)->create(['name' => 'ProduitUnique']);

    $response = getJson(route('businesses.products.index', $this->business).'?filter_query=ProduitUnique', [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    $ids = collect($response->json('data'))->pluck('id');
    $matchingProduct = Product::where('business_id', $this->business->id)
        ->where('name', 'ProduitUnique')
        ->first();

    expect($ids)->toContain($matchingProduct->id);
});
