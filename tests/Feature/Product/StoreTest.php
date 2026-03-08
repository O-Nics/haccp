<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;
use Domain\Product\Models\Product;

use function Pest\Laravel\postJson;

pest()->group('product');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request on products store', function () {
    postJson(route('businesses.products.store', $this->business))->assertUnauthorized();
});

it('rejects refresh token on products store route', function () {
    postJson(route('businesses.products.store', $this->business), [], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids products store when user is not a member of the business', function () {
    postJson(route('businesses.products.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids products store when member has no product.create permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    postJson(route('businesses.products.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('passes policy when member has product.create permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_CREATE);

    // Policy passée → validation échoue (pas de body) → 422, pas 403
    postJson(route('businesses.products.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable();
});

// --- VALIDATION ---

it('requires name on products store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_CREATE);

    postJson(route('businesses.products.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('rejects name exceeding max length on products store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_CREATE);

    postJson(route('businesses.products.store', $this->business), [
        'name' => str_repeat('a', 256),
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

// --- BEHAVIOR ---

it('creates a product with the correct data in database', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_CREATE);

    postJson(route('businesses.products.store', $this->business), [
        'name' => 'Poulet rôti',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    expect(
        Product::where('business_id', $this->business->id)
            ->where('name', 'Poulet rôti')
            ->exists()
    )->toBeTrue();
});

it('returns 201 with product structure on successful store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_CREATE);

    postJson(route('businesses.products.store', $this->business), [
        'name' => 'Poulet rôti',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'name'],
        ]);
});

it('associates the product with the correct business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_CREATE);

    postJson(route('businesses.products.store', $this->business), [
        'name' => 'Salade César',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    $product = Product::where('name', 'Salade César')->first();

    expect($product->business_id)->toBe($this->business->id);
});
