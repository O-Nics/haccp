<?php

declare(strict_types=1);

namespace Tests\Feature\Product;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;
use Domain\Product\Models\Product;

use function Pest\Laravel\patchJson;

pest()->group('product');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
    $this->product = Product::factory()->withBusiness($this->business)->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request on products update', function () {
    patchJson(route('businesses.products.update', [$this->business, $this->product]))->assertUnauthorized();
});

it('rejects refresh token on products update route', function () {
    patchJson(route('businesses.products.update', [$this->business, $this->product]), [], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids products update when user is not a member of the business', function () {
    patchJson(route('businesses.products.update', [$this->business, $this->product]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids products update when member has no product.edit permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    patchJson(route('businesses.products.update', [$this->business, $this->product]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('passes policy when member has product.edit permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_EDIT);

    patchJson(route('businesses.products.update', [$this->business, $this->product]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();
});

// --- VALIDATION ---

it('rejects name exceeding max length on products update', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_EDIT);

    patchJson(route('businesses.products.update', [$this->business, $this->product]), [
        'name' => str_repeat('a', 256),
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

// --- BEHAVIOR ---

it('returns 200 with product structure on successful update', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_EDIT);

    patchJson(route('businesses.products.update', [$this->business, $this->product]), [
        'name' => 'Nouveau nom',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'name'],
        ]);

});

it('updates name when provided', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_EDIT);

    patchJson(route('businesses.products.update', [$this->business, $this->product]), [
        'name' => 'Filet de bœuf',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($this->product->refresh()->name)->toBe('Filet de bœuf');
});

it('does not update name when name is not provided', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_EDIT);

    $originalName = $this->product->name;

    patchJson(route('businesses.products.update', [$this->business, $this->product]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertOk();

    expect($this->product->refresh()->name)->toBe($originalName);
});

it('returns 404 when product belongs to a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::PRODUCT_EDIT);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $productFromOtherBusiness = Product::factory()->withBusiness($otherBusiness)->create();

    patchJson(route('businesses.products.update', [$this->business, $productFromOtherBusiness]), [
        'name' => 'Nouveau nom',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNotFound();
});
