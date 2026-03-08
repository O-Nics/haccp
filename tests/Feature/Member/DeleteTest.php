<?php

declare(strict_types=1);

namespace Tests\Feature\Member;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Organisation;
use Domain\Business\Models\Role;

use function Pest\Laravel\deleteJson;

pest()->group('business');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
    $this->targetUser = User::factory()->create();
    $this->member = Member::factory()->withUser($this->targetUser)->withBusiness($this->business)->active()->create();
});

// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request on members delete', function () {
    deleteJson(route('businesses.members.delete', $this->business))->assertUnauthorized();
});

it('rejects refresh token on members delete route', function () {
    deleteJson(route('businesses.members.delete', $this->business), [], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids members delete when user is not a member of the business', function () {
    deleteJson(route('businesses.members.delete', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids members delete when member has no members.remove permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    deleteJson(route('businesses.members.delete', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('passes policy when member has members.remove permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.members.delete', $this->business), [
        'member_ids' => [$this->member->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();
});

// --- VALIDATION ---

it('requires member_ids on members delete', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.members.delete', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['member_ids']);
});

it('rejects member_ids when not an array on members delete', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.members.delete', $this->business), [
        'member_ids' => 'not-an-array',
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['member_ids']);
});

// --- BEHAVIOR ---

it('deletes a member and returns 204', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.members.delete', $this->business), [
        'member_ids' => [$this->member->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Member::find($this->member->id))->toBeNull();
});

it('deletes multiple members in a single request', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    $secondUser = User::factory()->create();
    $secondMember = Member::factory()->withUser($secondUser)->withBusiness($this->business)->active()->create();

    deleteJson(route('businesses.members.delete', $this->business), [
        'member_ids' => [$this->member->id, $secondMember->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Member::find($this->member->id))->toBeNull()
        ->and(Member::find($secondMember->id))->toBeNull();
});

it('soft deletes the member record', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.members.delete', $this->business), [
        'member_ids' => [$this->member->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNoContent();

    expect(Member::withTrashed()->find($this->member->id))->not->toBeNull()
        ->and(Member::withTrashed()->find($this->member->id)->deleted_at)->not->toBeNull();
});

it('returns 409 when user tries to delete themselves', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    $selfMember = Member::where('user_id', $this->user->id)
        ->where('business_id', $this->business->id)
        ->first();

    deleteJson(route('businesses.members.delete', $this->business), [
        'member_ids' => [$selfMember->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertConflict();
});

it('returns 409 when the member is the owner of the business organisation', function () {
    $ownerUser = User::factory()->create();
    $organisation = Organisation::factory()->withOwner($ownerUser)->create();
    $business = Business::factory()->withOrganisation($organisation)->withBusinessType()->create();

    $ownerMember = Member::factory()->withUser($ownerUser)->withBusiness($business)->active()->create();

    authorizeUser($this->user, $business, PermissionEnum::MEMBERS_REMOVE);

    deleteJson(route('businesses.members.delete', $business), [
        'member_ids' => [$ownerMember->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertConflict();
});

it('returns 404 when a member belongs to a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $memberFromOtherBusiness = Member::factory()->withUser($this->targetUser)->withBusiness($otherBusiness)->active()->create();

    deleteJson(route('businesses.members.delete', $this->business), [
        'member_ids' => [$memberFromOtherBusiness->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNotFound();
});

it('does not delete any member when one member belongs to a different business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_REMOVE);

    $otherBusiness = Business::factory()->withOrganisation()->withBusinessType()->create();
    $otherUser = User::factory()->create();
    $memberFromOtherBusiness = Member::factory()->withUser($otherUser)->withBusiness($otherBusiness)->active()->create();

    deleteJson(route('businesses.members.delete', $this->business), [
        'member_ids' => [$this->member->id, $memberFromOtherBusiness->id],
    ], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertNotFound();

    // Validation runs before any deletion — this->member must still exist
    expect(Member::find($this->member->id))->not->toBeNull();
});
