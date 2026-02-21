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

## Critical Gotchas

1. **NEVER** extend `FormRequest` → always `BaseFormRequest`
2. **NEVER** extend `JsonResource` → always `BaseJsonResource`
3. **NEVER** forget `declare(strict_types=1);` in every file
4. **ALWAYS** `final readonly class` for Actions and Repositories
5. **ALWAYS** UUID v7 for new primary keys
6. **ALWAYS** extend `BaseModel` or `BaseAuthenticable` for models
7. **ALWAYS** bind repos in `RepositoryServiceProvider` (not `AppServiceProvider`)
8. **ALWAYS** register Events in `AppServiceProvider::boot()`
9. Actions ARE the service layer — there is no separate `Services/` directory
10. Table is `auth_sessions` (not `sessions`) — avoids Laravel session conflict
11. `Cache::flush()` in `beforeEach()` when testing throttle (cache persists between tests)

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