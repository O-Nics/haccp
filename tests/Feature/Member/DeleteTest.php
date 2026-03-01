<?php

declare(strict_types=1);

namespace Tests\Feature\Member;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Organisation;
use Domain\Business\Models\Role;

use function Pest\Laravel\postJson;

pest()->group('business');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
    $this->targetUser = User::factory()->create();
    $this->member = Member::factory()->withUser($this->targetUser)->withBusiness($this->business)->active()->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request on members delete', function () {
    postJson(route('businesses.members.delete', [$this->business, $this->member]))->assertUnauthorized();
});

it('rejects refresh token on members delete route', function () {
    postJson(route('businesses.members.delete', [$this->business, $this->member]), [], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids members delete when user is not a member of the business', function () {
    postJson(route('businesses.members.delete', [$this->business, $this->member]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids members delete when member has no members.remove permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    postJson(route('businesses.members.delete', [$this->business, $this->member]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids members delete when user tries to delete themselves', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    $selfMember = Member::where('user_id', $this->user->id)
        ->where('business_id', $this->business->id)
        ->first();

    postJson(route('businesses.members.delete', [$this->business, $selfMember]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('passes policy when member has members.remove permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    postJson(route('businesses.members.delete', [$this->business, $this->member]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();
});

// --- BEHAVIOR ---

it('deletes a member and returns 204', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    postJson(route('businesses.members.delete', [$this->business, $this->member]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Member::find($this->member->id))->toBeNull();
});

it('soft deletes the member record', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    postJson(route('businesses.members.delete', [$this->business, $this->member]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Member::withTrashed()->find($this->member->id))->not->toBeNull()
        ->and(Member::withTrashed()->find($this->member->id)->deleted_at)->not->toBeNull();
});

it('returns 409 when the member is the owner of the business organisation', function () {
    $ownerUser = User::factory()->create();
    $organisation = Organisation::factory()->withOwner($ownerUser)->create();
    $business = Business::factory()->withOrganisation($organisation)->withBusinessType()->create();

    $ownerMember = Member::factory()->withUser($ownerUser)->withBusiness($business)->active()->create();

    authorizeUser($this->user, $business, PermissionEnum::MEMBERS_REMOVE);

    postJson(route('businesses.members.delete', [$business, $ownerMember]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertConflict();
});

it('returns 404 when the member belongs to a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $memberFromOtherBusiness = Member::factory()->withUser($this->targetUser)->withBusiness($otherBusiness)->active()->create();

    postJson(route('businesses.members.delete', [$this->business, $memberFromOtherBusiness]), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNotFound();
});
