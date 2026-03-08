<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;
use Domain\Product\Models\Product;

use function Pest\Laravel\deleteJson;

pest()->group('product');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
    $this->product = Product::factory()->withBusiness($this->business)->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request on products delete', function () {
    deleteJson(route('businesses.products.delete', $this->business))->assertUnauthorized();
});

it('rejects refresh token on products delete route', function () {
    deleteJson(route('businesses.products.delete', $this->business), [], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids products delete when user is not a member of the business', function () {
    deleteJson(route('businesses.products.delete', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids products delete when member has no product.delete permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    deleteJson(route('businesses.products.delete', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('passes policy when member has product.delete permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_DELETE);

    deleteJson(route('businesses.products.delete', $this->business), [
        'product_ids' => [$this->product->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();
});

// --- VALIDATION ---

it('requires product_ids on products delete', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_DELETE);

    deleteJson(route('businesses.products.delete', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['product_ids']);
});

it('rejects product_ids when not an array on products delete', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_DELETE);

    deleteJson(route('businesses.products.delete', $this->business), [
        'product_ids' => 'not-an-array',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['product_ids']);
});

// --- BEHAVIOR ---

it('deletes a product and returns 204', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_DELETE);

    deleteJson(route('businesses.products.delete', $this->business), [
        'product_ids' => [$this->product->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Product::find($this->product->id))->toBeNull();
});

it('deletes multiple products in a single request', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_DELETE);

    $secondProduct = Product::factory()->withBusiness($this->business)->create();

    deleteJson(route('businesses.products.delete', $this->business), [
        'product_ids' => [$this->product->id, $secondProduct->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Product::find($this->product->id))->toBeNull()
        ->and(Product::find($secondProduct->id))->toBeNull();
});

it('soft deletes the product record', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_DELETE);

    deleteJson(route('businesses.products.delete', $this->business), [
        'product_ids' => [$this->product->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Product::withTrashed()->find($this->product->id))->not->toBeNull()
        ->and(Product::withTrashed()->find($this->product->id)->deleted_at)->not->toBeNull();
});

it('returns 404 when a product belongs to a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_DELETE);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $productFromOtherBusiness = Product::factory()->withBusiness($otherBusiness)->create();

    deleteJson(route('businesses.products.delete', $this->business), [
        'product_ids' => [$productFromOtherBusiness->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNotFound();
});

it('does not delete any product when one product belongs to a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_DELETE);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $productFromOtherBusiness = Product::factory()->withBusiness($otherBusiness)->create();

    deleteJson(route('businesses.products.delete', $this->business), [
        'product_ids' => [$this->product->id, $productFromOtherBusiness->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNotFound();

    // Validation runs before any deletion — this->product must still exist
    expect(Product::find($this->product->id))->not->toBeNull();
});
