# CLAUDE.md

Guidance for Claude Code. Testing conventions → `docs/TESTING.md`.

## Project

Multi-tenant restaurant HACCP management system.
**Stack**: Laravel 12 + PHP 8.4 + Sanctum + Pest
**API Consumers**: iOS/Android apps, Web dashboard
**Security**: High (food safety) — soft deletes required (regulatory)

## Commands

```bash
composer test                           # All tests
php artisan test --filter=LoginTest     # Specific test
php artisan test --filter=auth          # By group
./vendor/bin/pint                       # Format (PSR-12)
php artisan route:list --path=api/auth  # List routes
php artisan pail --filter=auth          # Real-time logs
php artisan db:table auth_sessions      # Inspect table
```

## Architecture (DDD — 3 Layers)

```
app/                              ← Presentation Layer
├── Core/                         ← Shared infra (base classes, middleware, providers)
│   ├── Models/                   ← BaseModel, BaseAuthenticable, BasePivot
│   ├── Requests/                 ← BaseFormRequest
│   ├── Resources/                ← BaseJsonResource, BaseAnonymousCollection
│   ├── Middlewares/              ← ThrottleAuth, SecureHeaders, EnforceExpectsJson, IdentifyRequest
│   ├── Actions/                  ← BaseAction
│   └── Providers/                ← AppServiceProvider, RepositoryServiceProvider
└── Modules/{Domain}/             ← HTTP layer per domain
    ├── Controllers/
    ├── Requests/
    └── Resources/

src/
├── Domain/{Domain}/              ← Business Logic (no Framework deps)
│   ├── Actions/                  ← Use cases (final readonly class)
│   ├── Data/                     ← DTOs (Spatie LaravelData)
│   ├── Events/                   ← Domain events
│   ├── Listeners/
│   ├── Models/                   ← Eloquent models
│   ├── Interfaces/               ← Repository interfaces
│   ├── Exceptions/
│   └── Database/Factories/
└── Infrastructure/{Domain}/
    └── Repositories/             ← Concrete implementations
```

## File Patterns (Exact Paths)

| Layer | Path Pattern | Example |
|-------|-------------|---------|
| Controller | `app/Modules/{Domain}/Controllers/{Name}Controller.php` | `AuthController` |
| Request | `app/Modules/{Domain}/Requests/{Action}Request.php` | `LoginRequest` |
| Resource | `app/Modules/{Domain}/Resources/{Name}Resource.php` | `AuthResponseResource` |
| Action | `src/Domain/{Domain}/Actions/{Verb}{Noun}Action.php` | `LoginUserAction` |
| DTO | `src/Domain/{Domain}/Data/{Name}Data.php` | `LoginData`, `AuthData` |
| Event | `src/Domain/{Domain}/Events/{PastTense}Event.php` | `UserRegisteredEvent` |
| Listener | `src/Domain/{Domain}/Listeners/{Verb}{Noun}Listener.php` | `NewDeviceDetectedListener` |
| Model | `src/Domain/{Domain}/Models/{Model}.php` | `User`, `Device`, `Session` |
| Repo Interface | `src/Domain/{Domain}/Interfaces/{Model}RepositoryInterface.php` | `UserRepositoryInterface` |
| Repo Impl | `src/Infrastructure/{Domain}/Repositories/{Model}Repository.php` | `UserRepository` |
| Factory | `src/Domain/{Domain}/Database/Factories/{Model}Factory.php` | `UserFactory` |
| Route | `routes/api/{domain}.php` | `auth.php`, `businesses.php` |
| Test | `tests/Feature/{Domain}/{Name}Test.php` | `LoginTest.php` |

## Request → Response Flow

```
Route → [ThrottleAuth?] → [auth:sanctum?] → [ability:X?]
→ {Action}Request::validate() → toDto() → {Name}Data
→ Controller → {Verb}{Noun}Action::handle(Data)
→ Repository calls + Events + Notifications
→ {Name}Data (DTO) → {Name}Resource → { "data": { ... } }
```

## Base Classes (Must Extend)

**BaseModel** (`app/Core/Models/BaseModel.php`):
- `filtered($query, $search, $columns)` — multi-column search
- `order($query, $sort, $order)` — sorting with fallback
- `betweenDates($column, $start, $end)` — date range filter
- `forPeriod($start, $end, $colStart, $colEnd)` — period overlap

**BaseFormRequest** (`app/Core/Requests/BaseFormRequest.php`):
- `value($key)` → value or `Optional`
- `valueEnum($key, $enumClass)` → enum or `Optional`
- `valueCarbon($key)` → Carbon or `Optional`
- `valueFile($key)` → file or `Optional`
- Trait: `NormalizesEmail` (lowercase + trim)

**BaseJsonResource** (`app/Core/Resources/BaseJsonResource.php`):
- Auto-wraps all responses in `"data"` key
- Chainable: `->status(201)->message('Created')`

## Authentication Architecture

**Token Pair** (Sanctum, table: `personal_access_tokens`):
- Access: `config('auth.tokens.access.expiration')` min, ability: `access`
- Refresh: 30 days or 1 year (if `remember_me`), ability: `refresh`

**Session** (table: `auth_sessions`, NOT `sessions`):
- Links: `user_id → device_id → access_token_id + refresh_token_id`
- Revoke via `revoked_at` (soft delete) — never hard delete

**Rate Limiting** (`ThrottleAuth` middleware):
- Key: `auth-{prefix}:{sha1(ip)}:{sha1(email)}`
- Config: `config/auth.php` → `security.rate_limit.*`
- `.env.testing`: 5 attempts, 1 min decay, `array` cache store

## Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Controller | `{Name}Controller` | `AuthController`, `UserController` |
| Request | `{Action}Request` | `LoginRequest`, `RegisterRequest` |
| DTO | `{Name}Data` | `LoginData`, `AuthData`, `DeviceData` |
| Resource | `{Name}Resource` | `AuthResponseResource`, `UserResource` |
| Action | `{Verb}{Noun}Action` | `LoginUserAction`, `CreateAuthSessionAction` |
| Event | `{PastTense}Event` | `UserRegisteredEvent`, `NewDeviceDetectedEvent` |
| Listener | `{Verb}{Noun}Listener` | `NewDeviceDetectedListener` |
| Repo Interface | `{Model}RepositoryInterface` | `UserRepositoryInterface` |
| Enum | Singular + `Enum` suffix | `TokenAbilityEnum` |

## Repository Pattern

```php
// 1. Interface in src/Domain/{Domain}/Interfaces/
interface UserRepositoryInterface {
    public function findByEmail(string $email): ?User;
}

// 2. Implementation in src/Infrastructure/{Domain}/Repositories/
final readonly class UserRepository extends BaseRepository implements UserRepositoryInterface {}

// 3. Register as singleton in RepositoryServiceProvider
$this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
```

## Testing (Summary — Full Guide in docs/TESTING.md)

Each endpoint file tests these layers **in order**:
1. **Throttle** — rate limit triggers after N attempts (if `ThrottleAuth` on route)
2. **Auth middleware** — unauthenticated + wrong token ability (if protected route)
3. **Policy** — authorization (if Policy attached)
4. **Validation** — required fields, format, uniqueness, each rule separately
5. **Action behavior** — happy path + DB state + business logic
6. **Events & Notifications** — dispatched with correct data

## PHPDoc (MANDATORY on every public method)

Every public method **must** have a full PHPDoc block with:
1. **Short description** — one line, action-oriented verb (`Creates`, `Returns`, `Validates`, `Bulk-deletes`, etc.)
2. **Long description** — one or more sentences explaining business rules, edge cases, or important behaviour. Required unless the method is trivially obvious.
3. **`@param`** — one per parameter, describing what it represents (not just restating the type)
4. **`@return`** — describes what is returned and under what condition
5. **`@throws`** — one per exception with the HTTP code and the triggering condition

```php
/**
 * Bulk-deletes products for the given business.
 * Validates that all IDs belong to the business in a single query before deletion.
 * No product is deleted if any ID fails the ownership check.
 *
 * @param  DeleteProductData  $deleteProductData  The data containing the IDs of products to delete.
 * @param  Business           $business           The business the products must belong to.
 * @return void
 * @throws NotFoundException  404 if any product ID does not belong to the business.
 */
public function handle(DeleteProductData $deleteProductData, Business $business): void { ... }
```

**Rules:**
- Summary is **required** — one sentence max, no trailing period
- Long description is **required** unless the method is a trivial getter/delegator
- `@param` is **required** for every parameter
- `@return` is **required** — use `void` when applicable
- `@throws` is **required** for every exception the method can raise
- No Laravel boilerplate (`Display a listing of the resource`, `Store a newly created resource`, etc.)
- Align `@param` type columns for readability when there are multiple params

## Code Style (STRICT)

**Typing — always explicit:**
```php
// ✅
public function handle(LoginData $data): AuthData { ... }
function loginViaApi(User $user, ?Device $device = null): array { ... }

// ❌
public function handle($data) { ... }
function loginViaApi($user, $device = null) { ... }
```

**No arrow functions — always full closures:**
```php
// ✅
Event::assertDispatched(UserRegisteredEvent::class, function (UserRegisteredEvent $event) use ($user): bool {
    return $event->user->id === $user->id;
});

// ❌
Event::assertDispatched(UserRegisteredEvent::class, fn($e) => $e->user->id === $user->id);
```

**Descriptive variable names — never abbreviations:**
```php
// ✅
->where(function (Builder $query) use ($token): void {
    $query->where('access_token_id', $token->id);
})

// ❌
->where(fn($q) => $q->where('access_token_id', $token->id))
->where(function ($q) use ($t) { $q->where('access_token_id', $t->id); })
```

## Homogeneity Rules (STRICT)

These conventions exist across all domains — **never introduce exceptions**:

**Namespaces:**
- Actions: `Domain\{Domain}\Actions\` (plural — never `Action\`)
- All other DDD layers follow the same plural pattern

**Repository methods — standard names across all interfaces:**
| Method | Signature pattern |
|--------|------------------|
| `getFilteredPaginated` | `(IndexData, Business, ?array): LengthAwarePaginator` |
| `findById` | `(int\|string, ?array): ?Model` |
| `findByIdsForBusiness` | `(array, Business): Collection` |
| `store` | `(StoreData, Business): Model` |
| `update` | `(UpdateData, Model): Model` |
| `delete` | `(Model): void` |
| `deleteMany` | `(Collection): void` |

**Request field naming for bulk operations:**
- `{model}_ids` → e.g. `product_ids`, `role_ids`, `member_ids`
- Flat array of strings, never nested (`products.*.id` is wrong)

**Controller methods — standard names:**
- `index`, `store`, `update`, `deleteMany` (bulk — used for all delete routes)

**Route abilities — standard names:**
- `view-any`, `create`, `update`, `delete-many`

**Requests — always `final class`**

**Repositories — always `extends BaseRepository`**

**Data classes (DTOs) — never add `toDto()`**
- DTOs are already the DTO — `toDto()` that returns itself is an anti-pattern

## Critical Gotchas

1. **NEVER** extend `FormRequest` → always `BaseFormRequest`
2. **NEVER** extend `JsonResource` → always `BaseJsonResource`
3. **NEVER** forget `declare(strict_types=1);` in every file
4. **NEVER** use arrow functions (`fn()`) — always full closures with typed params
5. **NEVER** abbreviate variable names (`$q`, `$u`, `$n`, `$e`, `$t`, `$r`, `$i`)
6. **ALWAYS** `final readonly class` for Actions and Repositories
7. **ALWAYS** UUID v7 for new primary keys
8. **ALWAYS** extend `BaseModel` or `BaseAuthenticable` for models
9. **ALWAYS** bind repos in `RepositoryServiceProvider` (not `AppServiceProvider`)
10. **ALWAYS** register Events in `AppServiceProvider::boot()`
11. **ALWAYS** type every parameter and return type
12. Actions ARE the service layer — there is no separate `Services/` directory
13. Table is `auth_sessions` (not `sessions`) — avoids Laravel session conflict
14. `Cache::flush()` in `beforeEach()` when testing throttle (cache persists between tests)

## Adding a New Feature (Checklist)

1. Migration (UUID v7 PK, indexes on FKs, SoftDeletes if needed)
2. Model → `BaseModel`, `HasFactory`, define relationships
3. Factory → `src/Domain/{Domain}/Database/Factories/`
4. Repository Interface → `src/Domain/{Domain}/Interfaces/`
5. Repository Implementation → `src/Infrastructure/{Domain}/Repositories/`
6. Bind in `RepositoryServiceProvider` as singleton
7. DTO(s) → `src/Domain/{Domain}/Data/` (Spatie Data)
8. Action(s) → `final readonly class`, single `handle()` method
9. FormRequest → extend `BaseFormRequest`, add `toDto()` method
10. Controller → extend `BaseController`, use `BaseJsonResource`
11. Resource → extend `BaseJsonResource`
12. Route → `routes/api/{domain}.php`
13. Events/Listeners → register in `AppServiceProvider::boot()`
14. **Tests** → `tests/Feature/{Domain}/` following `docs/TESTING.md`

## External Docs

- [Spatie Laravel Data](https://spatie.be/docs/laravel-data) — DTOs
- [Scramble OpenAPI](https://scramble.dedoc.co/) — `/docs/api`
- [Pest PHP](https://pestphp.com/) — Testing framework
- [Laravel Sanctum](https://laravel.com/docs/sanctum) — API auth