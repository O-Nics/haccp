<?php

declare(strict_types=1);

namespace Tests\Feature\Member;

use Domain\Auth\Models\User;
use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;

use Domain\Auth\Models\User as AuthUser;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Domain\Business\Events\MemberInvitedEvent;
use Domain\Business\Notifications\MemberInvitedNotification;
use Domain\Business\Notifications\MemberInvitedWithPasswordNotification;

use function Pest\Laravel\postJson;

pest()->group('business');

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->business = Business::factory()->withOrganisation()->withBusinessType()->create();
});
// --- AUTH MIDDLEWARE ---

it('rejects unauthenticated request', function () {
    postJson(route('businesses.members.store',$this->business))->assertUnauthorized();
});

it('rejects refresh token on members store route', function () {
    postJson(route('businesses.members.store', $this->business), [], [
        'Authorization' => 'Bearer '.refreshToken($this->user),
    ])->assertForbidden();
});

// --- POLICY ---

it('forbids members store when user is not a member of the business', function () {
    postJson(route('businesses.members.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('forbids members store when member has no members.invite permission', function () {
    $roleWithoutPermission = Role::factory()->withBusiness($this->business)->create();
    Member::factory()->withUser($this->user)->withBusiness($this->business)->withRole($roleWithoutPermission)->active()->create();

    postJson(route('businesses.members.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertForbidden();
});

it('passes policy when member has members.invite permission', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    // Policy passée → validation échoue (pas de body) → 422, pas 403
    postJson(route('businesses.members.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable();
});

// --- VALIDATION ---

it('requires email first_name and last_name', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    postJson(route('businesses.members.store', $this->business), [], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'first_name', 'last_name']);
});

it('rejects invalid email format on members store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    postJson(route('businesses.members.store', $this->business), ['email' => 'not-an-email', 'first_name' => 'Alice', 'last_name' => 'Dupont'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects email exceeding max length on members store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    postJson(route('businesses.members.store', $this->business), ['email' => str_repeat('a', 250).'@test.com', 'first_name' => 'Alice', 'last_name' => 'Dupont'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('rejects first_name exceeding max length on members store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    postJson(route('businesses.members.store', $this->business), ['email' => 'alice@test.com', 'first_name' => str_repeat('a', 256), 'last_name' => 'Dupont'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['first_name']);
});

it('rejects last_name exceeding max length on members store', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    postJson(route('businesses.members.store', $this->business), ['email' => 'alice@test.com', 'first_name' => 'Alice', 'last_name' => str_repeat('a', 256)], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['last_name']);
});

// --- BEHAVIOR ---

it('creates a new user and member when email does not exist', function () {
    Notification::fake();
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    postJson(route('businesses.members.store', $this->business), ['email' => 'newuser@test.com', 'first_name' => 'Alice', 'last_name' => 'Dupont'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    expect(AuthUser::where('email', 'newuser@test.com')->exists())->toBeTrue()
        ->and(Member::where('business_id', $this->business->id)->whereHas('user', function (\Illuminate\Database\Eloquent\Builder $query): void {
            $query->where('email', 'newuser@test.com');
        })->exists())->toBeTrue();
});

it('creates a member for an existing user without creating a new user', function () {
    Notification::fake();
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    $existingUser    = AuthUser::factory()->create(['email' => 'existing@test.com']);
    $userCountBefore = AuthUser::count();

    postJson(route('businesses.members.store', $this->business), ['email' => 'existing@test.com', 'first_name' => 'Bob', 'last_name' => 'Martin'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    expect(AuthUser::count())->toBe($userCountBefore)
        ->and(Member::where('business_id', $this->business->id)->where('user_id', $existingUser->id)->exists())->toBeTrue();
});

it('returns 409 when user is already a member of the business', function () {
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    $existingUser = AuthUser::factory()->create(['email' => 'existing@test.com']);
    Member::factory()->withUser($existingUser)->withBusiness($this->business)->invited()->create();

    postJson(route('businesses.members.store', $this->business), ['email' => 'existing@test.com', 'first_name' => 'Bob', 'last_name' => 'Martin'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertConflict();
});

it('returns 201 with member structure on successful invite', function () {
    Notification::fake();
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    postJson(route('businesses.members.store', $this->business), ['email' => 'newuser@test.com', 'first_name' => 'Alice', 'last_name' => 'Dupont'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated()
        ->assertJsonStructure([
            'data' => ['id', 'invited_at', 'joined_at', 'status'],
        ]);
});

it('dispatches MemberInvitedEvent with temporary password when new user is created', function () {
    Event::fake();
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    postJson(route('businesses.members.store', $this->business), ['email' => 'newuser@test.com', 'first_name' => 'Alice', 'last_name' => 'Dupont'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    Event::assertDispatched(MemberInvitedEvent::class, function (MemberInvitedEvent $event): bool {
        return $event->data->temporaryPassword !== null;
    });
});

it('dispatches MemberInvitedEvent without temporary password when user already exists', function () {
    Event::fake();
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    AuthUser::factory()->create(['email' => 'existing@test.com']);

    postJson(route('businesses.members.store', $this->business), ['email' => 'existing@test.com', 'first_name' => 'Bob', 'last_name' => 'Martin'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    Event::assertDispatched(MemberInvitedEvent::class, function (MemberInvitedEvent $event): bool {
        return $event->data->temporaryPassword === null;
    });
});

it('does not dispatch MemberInvitedEvent when user is already a member', function () {
    Event::fake();
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    $existingUser = AuthUser::factory()->create(['email' => 'existing@test.com']);
    Member::factory()->withUser($existingUser)->withBusiness($this->business)->invited()->create();

    postJson(route('businesses.members.store', $this->business), ['email' => 'existing@test.com', 'first_name' => 'Bob', 'last_name' => 'Martin'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertConflict();

    Event::assertNotDispatched(MemberInvitedEvent::class);
});

it('sends MemberInvitedWithPasswordNotification when new user is created', function () {
    Notification::fake();
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    postJson(route('businesses.members.store', $this->business), ['email' => 'newuser@test.com', 'first_name' => 'Alice', 'last_name' => 'Dupont'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    $invitedUser = AuthUser::where('email', 'newuser@test.com')->first();

    Notification::assertSentTo($invitedUser, MemberInvitedWithPasswordNotification::class);
});

it('sends MemberInvitedNotification when existing user is invited', function () {
    Notification::fake();
    authorizeUser($this->user, $this->business, PermissionEnum::MEMBERS_INVITE);

    $existingUser = AuthUser::factory()->create(['email' => 'existing@test.com']);

    postJson(route('businesses.members.store', $this->business), ['email' => 'existing@test.com', 'first_name' => 'Bob', 'last_name' => 'Martin'], [
        'Authorization' => 'Bearer '.accessToken($this->user),
    ])->assertCreated();

    Notification::assertSentTo($existingUser, MemberInvitedNotification::class);
});
