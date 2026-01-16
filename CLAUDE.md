# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Metadata
- **Purpose**: Multi-tenant restaurant HACCP management system
- **API Consumers**: Mobile apps (iOS/Android), Web dashboard
- **Scale**: Multi-device per user, multi-restaurant per user
- **Security Level**: High (food safety compliance)
- **Data Retention**: Soft deletes (regulatory requirements)
- **Tech**: Laravel 12 + PHP 8.4 + Sanctum + Multi-device auth

## Quick Reference
- **Authentication Flow**: AuthService → SessionService → Session (Device + Tokens)
- **Repositories**: `app/Features/{Domain}/Repositories/{Model}/` (interface + implementation)
- **DTOs**: `app/Features/{Domain}/Data/` (Spatie Data)
- **Base Classes**: `app/Core/Models/`, `app/Core/Requests/`, `app/Core/Resources/`
- **API Helpers**: `apiResponse()`, `apiSuccess()`, `apiError()` (auto-wraps in 'data')
- **Custom Commands**: `php artisan make:repository --crud`, `sessions:cleanup`
- **Tests**: Pest → `php artisan test --filter={name}`
- **API Docs**: `/docs/api` (Scramble auto-generated)

## File Patterns (Predictable Structure)
```
Controllers:  app/Http/Controllers/Api/{Domain}/{Action}Controller.php
Requests:     app/Http/Requests/{Domain}/{Action}Request.php
DTOs:         app/DataTransferObjects/{Domain}/{Name}Data.php
Services:     app/Services/{Domain}/{Domain}Service.php
Repositories: app/Repositories/{Model}/{Model}Repository[Interface].php
Resources:    app/Http/Resources/{Model}Resource.php
Events:       app/Events/{PastTense}.php (e.g., UserLoggedIn)
Listeners:    app/Listeners/{Verb}{Noun}.php (e.g., SendNewDeviceLoginEmail)
Routes:       routes/api/{domain}.php (modular: auth, devices, user, health)
```

## Workflow Requirements
- Update CLAUDE.md with architectural changes, new conventions, or important patterns
- Always ask clarifying questions if unsure about project specifics or in planning mode
- Maintain this file as a living document as the project evolves
- Use relative paths from project root when referencing files

## Common Commands

### Development
```bash
composer dev          # Full stack: server + queue + logs + vite
php artisan serve     # Development server
php artisan pail --filter=auth  # Monitor auth logs
composer test         # Run all tests
php artisan test --filter=LoginTest  # Run specific test
./vendor/bin/pint     # Code formatting (PSR-12)
```

### Setup
```bash
composer setup  # Full setup: deps, .env, key, migrate, npm build
```

### Custom Artisan Commands
```bash
php artisan make:repository {name} --crud  # Scaffold repository pattern
php artisan make:mailable {name}           # Create mailable
php artisan sessions:cleanup               # Clean expired/revoked sessions
php artisan ide-helper:models              # Regenerate PHPDoc
```

### Debugging
```bash
php artisan tinker
> Session::with('user', 'device')->active()->latest()->first()  # Check last active session (auth_sessions)
> User::find('uuid')->sessions()->active()->get()                # User's active sessions
> Device::with('sessions')->latest()->first()                    # Device with sessions

php artisan db:table auth_sessions         # Show auth_sessions table structure
php artisan route:list --path=api/auth     # List auth routes
php artisan pail --filter=auth             # Real-time auth logs
```

## Architecture Patterns

### Request/Response Flow (7 Layers)
1. **Routes** (`routes/api/*.php`) → Modular by domain
2. **Middleware** → ThrottleAuth, SecureHeaders, auth:sanctum
3. **Controllers** → Single-action or resourceful
4. **FormRequests** → Extend BaseFormRequest (Spatie Data integration)
5. **Services** → Business logic (`final readonly class`)
6. **Repositories** → Data access (interface + implementation)
7. **Resources** → API response formatting

### Repository Pattern (CRITICAL)
**Location**: `app/Repositories/{Model}/`
**Structure**: `{Model}RepositoryInterface.php` + `{Model}Repository.php`
**Binding**: MUST register in `AppServiceProvider::register()` as singleton:
```php
$this->app->singleton(
    UserRepositoryInterface::class,
    UserRepository::class
);
```
**Generation**: `php artisan make:repository {Model} --crud`
**Pattern**: All repositories are `final readonly class`

### Service Layer
**Location**: `app/Services/{Domain}/`
**Pattern**: `final readonly class` with constructor DI
**Key Services**:
- `AuthService`: login, register, refresh, logout → AuthData
- `SessionService`: createSession, refreshSession, revokeSession (manages token lifecycle)
- `DeviceService`: registerDevice, deleteDevice (fingerprinting only)
- `PasswordService`: Password reset flow

### Base Classes (MUST Extend)

**BaseModel** (`app/Models/Base/BaseModel.php`):
- `filtered($query, $searchableColumns)` - Multi-column search
- `order($sort, $order)` - Sorting with created_at fallback
- `betweenDates($column, $start, $end)` - Date range
- `forPeriod($start, $end, $colStart, $colEnd)` - Period overlap

**BaseFormRequest** (`app/Core/Requests/BaseFormRequest.php`):
- `value($key)` - Returns value or `Optional`
- `valueEnum($key, $enumClass)` - Parse enums
- `valueCarbon($key)` - Parse dates
- `valueFile($key)` - Handle uploads

**BaseJsonResource** (`app/Core/Resources/BaseJsonResource.php`):
- Automatically wraps responses in `data` key for consistent API structure
- All API resources MUST extend this class
- Example response: `{"data": { ... resource content ... }}`

### API Response Helpers

**Location**: `app/Core/Helpers/helpers.php` (autoloaded)

**apiResponse($data, $status = 200, $headers = [], $message = null)**:
- Main response helper for standardized API responses
- Automatically handles JsonResource (already wrapped in 'data')
- For non-Resource data, wraps in 'data' key
- Optional message parameter
```php
// With Resource
return apiResponse(new UserResource($user), 201);

// With array
return apiResponse(['key' => 'value'], 200, [], 'Success');
```

**apiSuccess($data, $message = null, $status = 200)**:
- Shorthand for successful responses
```php
return apiSuccess($user, 'User created successfully', 201);
```

**apiError($message, $status = 400, $errors = [])**:
- Standard error responses
```php
// Simple error
return apiError('Invalid credentials', 401);

// With validation errors
return apiError('Validation failed', 422, ['email' => ['Email is required']]);
```

## Authentication Architecture (Session-Based)

### Dual-Token System with Sessions
**Architecture**: User → Device → Session → Tokens
- **Access Token** (Sanctum): 15 min, stored in `personal_access_tokens`
- **Refresh Token**: 30 days / 1 year, bcrypt hashed in `auth_sessions.refresh_token_hash`
- **Session**: Source of truth, links user + device + token pair (table: `auth_sessions`)
- **Rotation**: Automatic on refresh (configurable: `config/auth.php`)

**Note**: Table nommée `auth_sessions` (pas `sessions`) pour éviter conflit avec sessions web Laravel

**Database Structure**:
```
auth_sessions table:
├── user_id → FK to users
├── device_id → FK to devices (unique per user)
├── access_token_id → FK to personal_access_tokens (nullable)
├── refresh_token_hash (bcrypt)
├── refresh_token_expires_at
├── last_used_at, revoked_at (soft delete)

devices table (fingerprinting only):
├── device_id (client UUID)
├── device_name, device_type, platform, browser
├── ip_address, user_agent, last_used_at
```

**Config** (`config/auth.php`):
```php
'tokens' => [
    'access' => ['expiration' => 15],        // minutes
    'refresh' => ['expiration' => 43200, 'rotation' => true],
    'remember' => ['expiration' => 525600],
],
'sessions' => [
    'cleanup_expired_after' => 7,           // days
    'max_concurrent_per_device' => 1,       // unique constraint enforced
],
'devices' => ['max_per_user' => null, 'force_logout_oldest' => true],
```

### Session Model Methods
**Location**: `app/Models/Session.php` (table: `auth_sessions`)
```php
setRefreshToken($token)         // Hash with bcrypt (pas SHA-256)
verifyRefreshToken($token)      // Constant-time comparison
isTokenExpired()                // Check refresh token expiration
isRevoked()                     // Check if session revoked
revoke()                        // Soft delete session (set revoked_at)
touchLastUsed()                 // Update last activity
static generateRefreshToken()   // Generate 64-char token
scopeActive($query)             // Query active sessions only
```

### Device Model (Fingerprinting Only)
**Location**: `app/Models/Device.php`
```php
sessions()                      // HasMany relationship to sessions
touchLastUsed()                 // Update last activity
```

**No token management**: Device model no longer handles tokens (moved to Session model)

### Multi-Tenant Restaurant System
**Models**: Restaurant, RestaurantUser (pivot), RestaurantRole, RestaurantType, Role, Permission
**User Relations**:
```php
$user->restaurantUsers()           // All associations
$user->restaurantUserInvitations() // Invitations sent
$user->ownedRestaurants()          // Owner of
```

## Naming Conventions (STRICT)

| Type | Pattern | Example |
|------|---------|---------|
| Controller | `{Action}Controller` | `LoginController` |
| Request | `{Action}Request` | `LoginRequest` |
| DTO | `{Action}Data` or `{Name}DTO` | `LoginData`, `TokenPairDTO` |
| Resource | `{Model}Resource` | `UserResource` |
| Repository | `{Model}Repository[Interface]` | `UserRepository` |
| Service | `{Domain}Service` | `AuthService` |
| Event | `{PastTense}` | `UserLoggedIn`, `DeviceRevoked` |
| Listener | `{Verb}{Noun}` | `SendNewDeviceLoginEmail` |
| Enum | `{Singular}` | `DeviceType`, `Platform` |

## Type Safety (PHP 8.4)

### REQUIRED in ALL Files
```php
declare(strict_types=1);
```

### Patterns
- **Services/Repositories**: `final readonly class` with constructor DI
- **Enums**: Backed enums for device_type, platform, etc.
- **Typed properties**: All class properties MUST have types
- **Return types**: All methods MUST declare return types
- **Constructor promotion**: Use for DI parameters

## Common Gotchas

1. **NEVER** use `FormRequest` directly → Always extend `BaseFormRequest`
2. **NEVER** use `JsonResource` directly → Always extend `BaseJsonResource`
3. **NEVER** forget `declare(strict_types=1);` at top of PHP files
4. **NEVER** forget to bind repositories in `AppServiceProvider::register()`
5. **ALWAYS** use `final readonly class` for services/repositories
6. **ALWAYS** use UUID v7 (not v4) for new ID fields
7. **ALWAYS** extend `BaseModel` or `BaseAuthenticable` for models
8. **ALWAYS** use `apiResponse()`, `apiSuccess()`, or `apiError()` for API responses
9. Session refresh tokens use **bcrypt** (Hash::make), passwords use **bcrypt** (via casting)
10. Soft deletes on User, Restaurant (regulatory), and Session (via revoked_at)
11. Session lifecycle: Create → Active → Revoked (never hard delete until cleanup)

## Adding New Features (Checklist)

1. **Model** → Migration (UUID v7 if primary key, indexes for foreign keys)
2. **Repository** → Interface + Implementation + Bind in `AppServiceProvider`
3. **DTO** → Using Spatie Data (`app/DataTransferObjects/`)
4. **FormRequest** → Extend `BaseFormRequest` (not `FormRequest`)
5. **Service** → `final readonly class` with constructor DI
6. **Controller** → Single-action or resourceful, use `apiResponse()` helpers
7. **Route** → Modular file (`routes/api/{domain}.php`)
8. **Resource** → Extend `BaseJsonResource` for consistent 'data' wrapping
9. **Tests** → Pest feature tests (`tests/Feature/`)
10. **Event/Listener** → If needed (register in `AppServiceProvider::boot()`)

## Performance Patterns

- Use eager loading in repositories to avoid N+1 queries
- Leverage `BaseModel` scopes: `filtered()`, `order()`, `betweenDates()`, `forPeriod()`
- Index: device_id, user_id, email, created_at, foreign keys
- UUIDs v7 for time-sorted queries (better than v4)
- Cache: Token config values from `config/auth.php`
- Soft deletes: User, Restaurant (don't permanently delete)

## Events & Listeners

**Pattern**: Registered in `AppServiceProvider::boot()`
```php
Event::listen(NewDeviceDetected::class, SendNewDeviceLoginEmail::class);
```

**Key Events**:
- `UserLoggedIn` - Successful login
- `NewDeviceDetected` - New device logs in → Sends email notification
- `DeviceRevoked` - Device removed

## Testing (Pest)

**Location**: `tests/Feature/` (HTTP), `tests/Unit/` (isolated)
**Pattern**:
```php
it('can login with valid credentials', function () {
    // Arrange, Act, Assert
})->group('auth');
```

**Run Tests**:
```bash
php artisan test --filter=LoginTest    # Specific test
php artisan test --filter=auth         # Group
php artisan test --parallel            # Parallel execution
```

## Agent Usage Guidelines

### When to Use Task/Explore Agents
- Multi-file refactoring across repositories
- Finding all implementations of a pattern (e.g., "all repositories")
- Analyzing authentication flow across services (multi-file)
- Security vulnerability scanning across codebase
- Checking consistency (e.g., "all FormRequests extend BaseFormRequest")

### When NOT to Use Agents
- Reading a single known file → Use `Read` tool
- Simple keyword searches → Use `Grep` tool
- Known file locations → Use direct tools
- Single-file edits → Use `Edit` tool

### Common Agent Tasks Examples
```
"Find all repositories that don't follow the {Model}Repository pattern"
"Analyze the complete authentication flow from LoginRequest to TokenPairDTO"
"Find all places where Device model is queried without eager loading"
"Check all FormRequests for consistency with BaseFormRequest usage"
"Find all controllers that don't use single-action pattern"
```

## Security

- **Rate Limiting**: `ThrottleAuth` middleware (5 attempts / 15 min)
- **Token Hashing**: Refresh tokens SHA-256, passwords bcrypt (auto via casting)
- **CORS**: Configure in `config/cors.php`
- **Headers**: `SecureHeaders` middleware
- **Email Verification**: `MustVerifyEmail` on User model

## Database

**UUIDs v7**: User IDs (time-sortable, better indexing than v4)
**Soft Deletes**: User, Restaurant (regulatory requirement)
**Indexes**: device_id, user_id, email, timestamps, foreign keys

**PHPDoc**: All models have extensive `@property`, `@method`, `@mixin` blocks for IDE support

## Environment Variables

```bash
# Token expiration (minutes)
ACCESS_TOKEN_EXPIRATION=15
REFRESH_TOKEN_EXPIRATION=43200    # 30 days
REMEMBER_TOKEN_EXPIRATION=525600  # 1 year

# Device limits
MAX_DEVICES_PER_USER=5

# Rate limiting
AUTH_RATE_LIMIT_ATTEMPTS=5
AUTH_RATE_LIMIT_DECAY=15
```

## External Dependencies Documentation

- [Spatie Laravel Data](https://spatie.be/docs/laravel-data) - DTO validation & transformation
- [Scramble](https://scramble.dedoc.co/) - OpenAPI documentation generation
- [Pest](https://pestphp.com/) - Testing framework
- [Laravel Sanctum](https://laravel.com/docs/12.x/sanctum) - API authentication
- [Laravel Pint](https://laravel.com/docs/12.x/pint) - Code style (PSR-12)

## Architectural Changes Log

- **2025-11-27**: Optimized CLAUDE.md for token efficiency and agent usage
- **2025-11-27**: Removed `make:all` command (use `make:repository --crud` instead)
- **2025-11-23**: Added Restaurant multi-tenant system with RBAC