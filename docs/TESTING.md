# Testing Conventions

All feature tests use Pest. Location: `tests/Feature/{Domain}/{Name}Test.php`.

## Global Setup (auto-applied via `tests/Pest.php`)

```php
pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)   // Fresh DB per test
    ->in('Feature')
    ->beforeEach(fn() => Mail::fake()); // Mails always faked
```

`TestCase::setUp()` disables Sanctum stateful auth: `config(['sanctum.stateful' => []])`.

## Standard Test Structure Per Endpoint

Every endpoint file tests these layers **in order**:

### 1. Throttle (if `ThrottleAuth` on route)

`Cache::flush()` is **required** in `beforeEach()` — the `array` cache persists between
tests in the same process run.

```php
beforeEach(function () {
    Cache::flush();
});

it('throttles after max attempts', function () {
    $max = config('auth.security.rate_limit.login_attempts'); // 5 in .env.testing

    for ($i = 0; $i < $max; $i++) {
        postJson(route('auth.login'), invalidPayload())->assertUnauthorized();
    }

    postJson(route('auth.login'), invalidPayload())
        ->assertTooManyRequests()
        ->assertJsonStructure(['message', 'retry_after']);
});
```

### 2. Auth Middleware (if route is protected)

Test both unauthenticated and wrong token ability:

```php
it('rejects unauthenticated request', function () {
    getJson(route('user.show'))->assertUnauthorized();
});

it('rejects access token on refresh route', function () {
    $token = $user->createToken('test', ['access'])->plainTextToken;
    postJson(route('auth.refresh'), [], ['Authorization' => "Bearer $token"])
        ->assertForbidden();
});
```

**Helper** — create a token with the right ability:

```php
function tokenFor(User $user, string $ability = 'access'): string
{
    return $user->createToken('test', [$ability])->plainTextToken;
}

// In test:
$headers = ['Authorization' => 'Bearer ' . tokenFor($user)];
getJson(route('user.show'), $headers)->assertOk();
```

### 3. Policy (if Policy attached)

```php
it('forbids access to another user resource', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    getJson(route('resource.show', $other), ['Authorization' => 'Bearer ' . tokenFor($owner)])
        ->assertForbidden();
});

it('allows access to own resource', function () {
    $user = User::factory()->create();

    getJson(route('resource.show', $user), ['Authorization' => 'Bearer ' . tokenFor($user)])
        ->assertOk();
});
```

### 4. Validation

Test **each rule separately** — required, format, max, unique, enum:

```php
it('requires email and password', function () {
    postJson(route('auth.login'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

it('rejects invalid email format', function () {
    postJson(route('auth.login'), ['email' => 'not-an-email', 'password' => 'password', ...])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires device_id and device_platform', function () {
    postJson(route('auth.login'), ['email' => 'x@x.com', 'password' => 'password'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['device_id', 'device_platform']);
});
```

### 5. Action Behavior (Happy Path)

Test three things: **response structure**, **DB state**, **business logic**:

```php
// Response structure
it('returns correct structure on valid login', function () {
    postJson(route('auth.login'), validPayload())
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'email', 'first_name', 'last_name'],
                'tokens' => ['access', 'refresh'],
            ],
        ]);
});

// DB state
it('creates device and session on login', function () {
    $user = User::factory()->create();
    $device = Device::factory()->make();

    postJson(route('auth.login'), payloadFor($user, $device))->assertOk();

    expect(Device::query()->where('user_id', $user->id)->count())->toBe(1)
        ->and(Session::query()->where('user_id', $user->id)->count())->toBe(1);
});

// Business logic
it('extends refresh token expiry when remember_me=true', function () {
    $response = postJson(route('auth.login'), [..., 'remember_me' => true])->assertOk();
    $expiresAt = $response->json('data.tokens.refresh.expires_at');
    $expected = now()->addMinutes(config('auth.tokens.remember.expiration'))->timestamp;

    expect($expiresAt)->toBeGreaterThanOrEqual($expected - 5)
        ->toBeLessThanOrEqual($expected + 5);
});
```

### 6. Events & Notifications

**Always** call `Event::fake()` or `Notification::fake()` **before** the action.
Assert both the positive case (dispatched) and negative case (not dispatched):

```php
it('dispatches NewDeviceDetectedEvent on new device', function () {
    Event::fake();

    postJson(route('auth.login'), validPayload())->assertOk();

    Event::assertDispatched(NewDeviceDetectedEvent::class);
});

it('does not dispatch event on known device', function () {
    Event::fake();
    Device::factory()->withUser($user)->create(['device_id' => $knownDeviceId]);

    postJson(route('auth.login'), payloadWith($knownDeviceId))->assertOk();

    Event::assertNotDispatched(NewDeviceDetectedEvent::class);
});

it('sends NewDeviceLoginNotification to user', function () {
    Notification::fake();

    postJson(route('auth.login'), validPayload())->assertOk();

    Notification::assertSentTo($user, NewDeviceLoginNotification::class,
        fn($n) => $n->device->model === $device->model
    );
});
```

Note: `Mail::fake()` is global (in `Pest.php`). Use `Notification::fake()` per-test.

---

## Complete File Example: Login

```php
<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Events\NewDeviceDetectedEvent;
use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Domain\Auth\Notifications\NewDeviceLoginNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\postJson;

pest()->group('auth');

beforeEach(function () {
    Cache::flush();
    $this->user = User::factory()->create();
    $this->device = Device::factory()->make();
});

// --- THROTTLE ---

it('throttles after max login attempts', function () {
    $max = config('auth.security.rate_limit.login_attempts');

    for ($i = 0; $i < $max; $i++) {
        postJson(route('auth.login'), [
            'email' => $this->user->email,
            'password' => 'wrongpassword',
            'device_id' => $this->device->device_id,
            'device_platform' => $this->device->platform,
            'device_model' => $this->device->model,
            'remember_me' => false,
        ])->assertUnauthorized();
    }

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'wrongpassword',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertTooManyRequests()
        ->assertJsonStructure(['message', 'retry_after']);
});

// --- VALIDATION ---

it('requires email and password', function () {
    postJson(route('auth.login'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email', 'password']);
});

it('rejects invalid email format', function () {
    postJson(route('auth.login'), [
        'email' => 'not-an-email',
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires device_id and device_platform', function () {
    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['device_id', 'device_platform']);
});

// --- BEHAVIOR ---

it('returns 401 on wrong credentials', function () {
    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'wrongpassword',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertUnauthorized();
});

it('returns correct structure on valid login', function () {
    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertOk()
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'email', 'first_name', 'last_name'],
                'tokens' => ['access', 'refresh'],
            ],
        ]);
});

it('creates device and session on login', function () {
    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertOk();

    expect(Device::query()->where('user_id', $this->user->id)->count())->toBe(1)
        ->and(Session::query()->where('user_id', $this->user->id)->count())->toBe(1);
});

// --- EVENTS & NOTIFICATIONS ---

it('dispatches NewDeviceDetectedEvent on new device', function () {
    Event::fake();

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertOk();

    Event::assertDispatched(NewDeviceDetectedEvent::class);
});

it('does not dispatch event when device is already known', function () {
    Event::fake();
    Device::factory()->withUser($this->user)->create([
        'device_id' => $this->device->device_id,
    ]);

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertOk();

    Event::assertNotDispatched(NewDeviceDetectedEvent::class);
});

it('sends NewDeviceLoginNotification to user', function () {
    Notification::fake();

    postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'password',
        'device_id' => $this->device->device_id,
        'device_platform' => $this->device->platform,
        'device_model' => $this->device->model,
        'remember_me' => false,
    ])->assertOk();

    Notification::assertSentTo($this->user, NewDeviceLoginNotification::class,
        fn($n) => $n->device->model === $this->device->model
    );
});
```

---

## Factories Quick Reference

```php
User::factory()->create()                           // Random user (password: 'password')
User::factory()->create(['email' => 'x@x.com'])    // Specific email
User::factory()->unverified()->create()             // email_verified_at = null
User::factory()->verified()->create()              // email_verified_at = now()

Device::factory()->make()                          // In-memory (no DB save)
Device::factory()->create()                        // Persisted
Device::factory()->withUser($user)->create()       // Attached to specific user
Device::factory()->rememberMe()->create()          // remember_me = true
```

## Assertions Cheat Sheet

```php
// Status
->assertOk()                                    // 200
->assertCreated()                               // 201
->assertNoContent()                             // 204
->assertUnauthorized()                          // 401
->assertForbidden()                             // 403
->assertUnprocessable()                         // 422
->assertTooManyRequests()                       // 429

// Response body
->assertJsonStructure(['data' => ['key', ...]])
->assertJsonValidationErrors(['field1', 'field2'])
->assertJson(['key' => 'value'])               // Partial match

// Events
Event::assertDispatched(MyEvent::class)
Event::assertDispatched(MyEvent::class, fn($e) => $e->user->id === $user->id)
Event::assertNotDispatched(MyEvent::class)

// Notifications
Notification::assertSentTo($user, MyNotification::class)
Notification::assertSentTo($user, MyNotification::class, fn($n) => $n->device->id === $id)
Notification::assertNothingSent()

// Expectations
expect($collection)->toHaveCount(2)
expect($value)->toBeInstanceOf(User::class)
expect($timestamp)->toBeGreaterThanOrEqual($expected - 5)->toBeLessThanOrEqual($expected + 5)
```
