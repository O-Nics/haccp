# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Workflow Requirements
- Update CLAUDE.md with any significant architectural changes, new conventions, or important information.
- Ensure CLAUDE.md reflects the current state of the codebase for accurate assistance.
- Always ask for clarifying questions if unsure about project specifics or in planning mode.
- Use clear and concise language to describe complex concepts.
- Organize information into sections with headings for easy navigation.
- Include code snippets where relevant to illustrate commands or configurations.
- Highlight any custom tools, scripts, or configurations unique to this project.
- Document any third-party libraries or frameworks that are integral to the project.
- Provide context on the project's purpose, key features, and intended audience.
- Maintain this file as a living document, updating it as the project evolves.
- When referencing files or directories, use relative paths from the project root.
- Avoid jargon or abbreviations that may not be universally understood.
- Prioritize clarity and completeness to facilitate effective collaboration with Claude Code.

## Project Overview

Laravel 12 authentication starter with **multi-device support**, **access/refresh/remember tokens**, and **client-managed device IDs**. Built with PHP 8.4, emphasizing type safety (strict types, enums, DTOs, readonly classes).

### Core Features
- **Client-Side Device Management**: Device IDs generated and managed by client (mobile/web app)
- **Token-Based Expiration**: Tokens expire (access, refresh, remember), not devices
- **Flexible Device Limits**: Configurable max devices per user (null = unlimited)
- **Remember Me Support**: Extended token expiration for trusted devices
- **New Device Notifications**: Email alerts only on login from unknown devices (not on register)
- **Simple & Robust**: No fingerprinting, no security logs - focus on core auth only

## Development Commands

### Setup and Installation
```bash
composer setup              # Full setup: install deps, copy .env, generate key, migrate, build assets
composer install            # Install PHP dependencies
php artisan key:generate    # Generate app key
php artisan migrate         # Run database migrations
```

### Development Server
```bash
composer dev                # Concurrent: server + queue + logs + vite (port auto-assigned)
php artisan serve           # Development server only
php artisan queue:listen    # Queue worker
php artisan pail            # Real-time logs
npm run dev                 # Vite dev server
```

### Testing
```bash
composer test               # Clear config cache + run PHPUnit
php artisan test            # Run all tests
php artisan test --filter AuthTest  # Run specific test
```

### Code Quality
```bash
./vendor/bin/pint           # Laravel Pint (code formatting)
php artisan ide-helper:generate  # Generate IDE helper files
```

### API Documentation
```bash
# Access at /docs/api
# Powered by Scramble (dedoc/scramble)
# Automatically generates OpenAPI docs from routes/controllers
```

### Code Generation Commands
```bash
php artisan make:all Product        # Generate complete CRUD: Model, Migration, Factory, Controller, Service, Repository, DTOs, Requests, Resource, Tests
php artisan make:service Product    # Generate only the service class
php artisan make:repository Product # Generate repository interface + implementation

# make:all generates:
# - Model with scopes (filtered, order)
# - Migration & Factory
# - Controller (with Route Model Binding)
# - 3 Form Requests (Index, Store, Update) with toDto()
# - 3 DTOs (IndexData, StoreData, UpdateData)
# - Service with REST methods
# - Repository (Interface + Concrete)
# - Resource for JSON responses
# - 5 Test files (Index, Show, Store, Update, Destroy)
# - Auto-binds repository in AppServiceProvider
```

## Architecture Overview

### Authentication Flow
- **Service Layer Pattern**: `AuthService` and `DeviceService` handle all business logic
- **Sanctum-based**: Access tokens + refresh tokens (SHA-256 hashed)
- **Device Management**: Client-provided device IDs, server validates and tracks
- **Token Rotation**: Refresh tokens automatically rotate on use (configurable in `config/auth.php`)

### Key Components

**Controllers** (`app/Http/Controllers/Api/`)
- `AuthController`: register, login, refresh, logout, me
- `DeviceController`: index (list devices), destroy (revoke single device), revokeOthers (revoke all except current)

**Services** (`app/Services/`)
- `AuthService`: User authentication, token generation, remember me support
- `DeviceService`: Device registration/update, max device enforcement, token validation

**Models**
- `User` (uses `HasDevices` trait): Multi-device support
- `UserDevice`: Tracks devices with refresh tokens (tokens expire, not devices)

**Repositories** (`app/Repositories/`)
- `UserRepository`: User data access layer
- `UserDeviceRepository`: Device CRUD operations, find by user+device_id

**DTOs** (`app/DataTransferObjects/`)
- Type-safe data transfer using `Spatie\LaravelData\Data`
- `TokenPairDTO`: API response with access + refresh tokens
- `DeviceInfoData`: Client device information (device_id, device_name, device_type, platform, browser)
- Auth DTOs: `LoginData`, `RegisterData`, `RefreshTokenData`, `LogoutData`

**Events** (`app/Events/`)
- `UserLoggedIn`: Dispatched on successful login
- `SuspiciousLoginDetected`: Dispatched when login from NEW device (not on register)
- `DeviceRevoked`: For future use

**Listeners** (`app/Listeners/`)
- `SendNewDeviceLoginEmail`: Sends email when user logs in from new device (queued)

**Form Requests** (`app/Http/Requests/`)
- All inherit from `BaseFormRequest` which provides helper methods:
  - `value($key)`: Returns value or `Spatie\LaravelData\Optional`
  - `valueEnum($key, $enumClass)`: Type-safe enum extraction
  - `valueCarbon($key)`: Parse dates safely
  - `toDto()`: Convert to main DTO
  - `toDeviceDto()`: Convert to DeviceInfoData
  - `rememberMe()`: Boolean helper for remember_me checkbox

### Configuration

**`config/auth.php`** - All auth settings centralized here:

#### Token Configuration
```php
'tokens' => [
    'access' => [
        'expiration' => 15, // minutes
    ],
    'refresh' => [
        'expiration' => 43200, // 30 days in minutes
        'rotation' => true, // Rotate refresh token on each use
    ],
    'remember' => [
        'expiration' => 525600, // 1 year in minutes
    ],
],
```

#### Device Management
```php
'devices' => [
    'max_per_user' => null, // null = unlimited, int = max devices
    'force_logout_oldest' => true, // Auto-logout oldest when limit reached
],
```

#### Security/Rate Limiting
```php
'security' => [
    'rate_limit' => [
        'login_attempts' => 5,
        'lockout_minutes' => 15,
    ],
],
```

### Custom Stubs

Project includes customized artisan stubs in `/stubs/`:
- `request.stub`: Extends `BaseFormRequest` instead of `FormRequest`
- All default Laravel stubs are present for consistency

When creating new Form Requests:
```bash
php artisan make:request MyRequest
# Auto-generates with BaseFormRequest parent class
```

### Database Schema

**Users** (`users` table):
- Standard auth fields + OAuth support (google_id, facebook_id, apple_id)
- `last_login_at` tracking

**Devices** (`user_devices` table):
- `device_id` (string, unique per user) - PROVIDED BY CLIENT
- `device_name`, `device_type` (enum), `platform` (enum), `browser`, `ip_address`, `user_agent`
- `refresh_token_hash` (SHA-256)
- `token_expires_at` - When the refresh token expires
- `last_used_at`, `created_at`, `updated_at`
- Unique index: `(user_id, device_id)`

**Key Points**:
- Devices DON'T expire, only tokens do
- Client provides `device_id` on register/login
- Server updates existing device or creates new one
- `token_expires_at` determines refresh token validity

### Middleware

**`ThrottleAuth`** - Custom rate limiting middleware
- Applied to login endpoint
- Configurable in `config/auth.php`

### Routes Structure

**`routes/api.php`**:
- `POST /api/auth/register` - Create account (requires device_id, device_name, remember_me)
- `POST /api/auth/login` - Login (requires device_id, device_name, remember_me)
- `POST /api/auth/refresh` - Refresh tokens (requires device_id, refresh_token)
- `POST /api/auth/logout` - Logout current device (auth required)
- `GET /api/auth/me` - Get user info with devices (auth required)
- `GET /api/devices` - List all devices (auth required)
- `DELETE /api/devices/{deviceId}` - Revoke specific device (auth required)
- `POST /api/devices/revoke-others` - Revoke all except current (auth required, requires current_device_id)
- `GET /health` - Health check endpoint

### Testing Setup

- Pest PHP + PHPUnit configured
- SQLite in-memory database for tests
- Helper package: `spatie/test-time` for time manipulation
- Tests in `tests/Feature/Auth/` for auth flows

### PHP 8.4 & Laravel 12 Features Used

- **Strict types**: All files use `declare(strict_types=1)`
- **Property hooks** via `protected function casts(): array` (new Laravel 12 syntax)
- **Readonly classes**: DTOs and services are readonly
- **Enums**: `DeviceType`, `Platform` for type safety
- **Carbon 3**: Latest version with improved performance

### Key Conventions

1. **Type Safety**: Always use strict types, type hints, and return types
2. **Service Layer**: Business logic goes in services, controllers stay thin
3. **DTOs Everywhere**:
   - Use `Spatie\LaravelData\Data` for all DTOs
   - Every FormRequest must have `toDto()` method
   - Requests with device info should have `toDeviceDto()` method
   - Services accept DTOs, not individual parameters or arrays
   - Example:
     ```php
     // ✅ CORRECT
     public function login(LoginData $data, DeviceInfoData $deviceInfo, bool $rememberMe)

     // ❌ WRONG
     public function login(string $email, string $password, string $deviceId)
     ```
4. **No Mass Assignment / No Fillable**:
   - **NEVER** use `$fillable` or `$guarded` in models
   - **NEVER** use `Model::create(array)` or `$model->update(array)` or `$model->fill(array)`
   - **ALWAYS** assign properties individually with explicit typing
   - Example:
     ```php
     // ✅ CORRECT
     $device = new UserDevice;
     $device->device_id = $deviceInfo->deviceId;
     $device->device_name = $deviceInfo->deviceName;
     $device->save();

     // ❌ WRONG - Never do this
     $device = UserDevice::create([
         'device_id' => $deviceInfo->deviceId,
     ]);
     ```
5. **Form Requests**:
   - Extend `BaseFormRequest` for consistent validation handling
   - All requests must implement `toDto()` method returning a Data class
   - Device-aware requests must also implement `toDeviceDto(): DeviceInfoData`
6. **Eloquent Scopes**: Use query scopes (`activeToken()`, `expiredToken()`) on models
7. **Route Model Binding**: Use typed route parameters (e.g., `public function show(User $user)`)
8. **Events & Listeners**:
   - Events dispatched: `UserLoggedIn`, `SuspiciousLoginDetected` (login only, not register)
   - Listeners handle side effects (email) - keeps services clean
   - Email listener is queued for async processing
9. **Repository Pattern - Database Access Layer**:
   - **ALL database reads/writes MUST go through repositories**
   - **NEVER** call Eloquent methods directly in services, controllers, or models
   - Models should ONLY contain:
     - Relationships (e.g., `belongsTo()`, `hasMany()`)
     - Accessors/Mutators (e.g., `getFullNameAttribute()`)
     - Business logic helpers (e.g., `isTokenExpired()`, `verifyRefreshToken()`)
     - NO database operations (no `save()`, `delete()`, `where()`, `find()`, etc.)
   - Services should ONLY call repository methods, never touch models directly
   - Example:
     ```php
     // ✅ CORRECT - In Service
     $this->userRepository->deleteTokensByDeviceId($user, $deviceId);
     $this->deviceRepository->delete($device);

     // ❌ WRONG - Never do this in services
     $user->tokens()->where('name', $deviceId)->delete();
     $device->delete();

     // ✅ CORRECT - In Model (business logic only)
     public function isTokenExpired(): bool {
         return $this->token_expires_at?->isPast() ?? false;
     }

     // ❌ WRONG - Never do this in models
     public function updateLastLogin(): void {
         $this->last_login_at = now();
         $this->save(); // Database operation!
     }
     ```
10. **Architecture Pattern**: Request → Controller → Service → Repository → Model + Events/Listeners
   - Controllers: HTTP handling only, no business logic, no database access
   - Services: Business logic, call repositories, dispatch events, NO direct database access
   - Repositories: ONLY place for database operations (queries, inserts, updates, deletes)
   - Models: Relationships, accessors, business logic helpers - NO database operations
   - Traits: Reusable code only (relationships), NO business logic, NO database operations

## Client Integration Guide

### Register Flow
```json
POST /api/auth/register
{
  "email": "user@example.com",
  "password": "SecureP@ss123",
  "password_confirmation": "SecureP@ss123",
  "device_id": "client-generated-uuid-or-string",
  "device_name": "iPhone 14 Pro",
  "device_type": "mobile",
  "platform": "ios",
  "browser": "Safari",
  "remember_me": false
}
```

**Response:**
```json
{
  "message": "User registered successfully",
  "access_token": "...",
  "access_token_expires_in": 900,
  "refresh_token": "...",
  "refresh_token_expires_in": 2592000,
  "device_id": "client-generated-uuid-or-string"
}
```

### Login Flow
```json
POST /api/auth/login
{
  "email": "user@example.com",
  "password": "SecureP@ss123",
  "device_id": "client-generated-uuid-or-string",
  "device_name": "iPhone 14 Pro",
  "device_type": "mobile",
  "platform": "ios",
  "browser": "Safari",
  "remember_me": true
}
```

**Notes:**
- If `device_id` exists for this user → updates device info, no email sent
- If `device_id` is new → creates device, sends "new device" email to user
- If `remember_me=true` → uses 1 year token expiration instead of 30 days

### Refresh Token Flow
```json
POST /api/auth/refresh
{
  "device_id": "client-generated-uuid-or-string",
  "refresh_token": "..."
}
```

**Response:** Same as login/register response

### Logout Flow
```json
POST /api/auth/logout
Headers: Authorization: Bearer {access_token}
{
  "device_id": "client-generated-uuid-or-string"
}
```

### Device Management
```json
GET /api/devices
Headers: Authorization: Bearer {access_token}

DELETE /api/devices/{deviceId}
Headers: Authorization: Bearer {access_token}

POST /api/devices/revoke-others
Headers: Authorization: Bearer {access_token}
{
  "current_device_id": "client-generated-uuid-or-string"
}
```
