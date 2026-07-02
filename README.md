# HACCP — Restaurant Food Safety Management API

A multi-tenant API for managing HACCP (Hazard Analysis and Critical Control Points) compliance in restaurants: organisations, businesses, team roles/permissions and product tracking, built on a strict Domain-Driven Design architecture.

Built with **Laravel 12** and **PHP 8.4**.

## ✨ Features

### Multi-device authentication
- Access + refresh tokens (Sanctum) with automatic rotation
- Device fingerprinting and per-device session tracking (`auth_sessions`)
- Rate limiting on auth endpoints (brute-force protection)
- Revoke a single device or all other devices at once

### Multi-tenant business management
- Organisations → Businesses hierarchy
- Per-business team members with custom **roles & permissions**
- Fine-grained authorization via policies (`view-any`, `create`, `update`, `delete-many`)

### Product tracking
- Per-business product catalog, foundation for HACCP traceability (expiry, temperature, critical control points)

### Engineering
- Domain-Driven Design: clean separation between `app/` (HTTP layer), `src/Domain` (business logic) and `src/Infrastructure` (repositories)
- DTOs via Spatie Laravel Data, Actions as use cases, Repository pattern
- Strict typing everywhere, UUID v7 primary keys, soft deletes (regulatory requirement)
- Tested with Pest/PHPUnit
- OpenAPI documentation generated with [Scramble](https://scramble.dedoc.co/)

## 📋 Requirements

- PHP 8.3 or 8.4
- MySQL 8.0+ / PostgreSQL 14+ / SQLite 3.35+
- Redis 6.0+ (recommended for caching/rate limiting)

## 🚀 Installation

```bash
git clone git@github.com:O-Nics/haccp.git
cd haccp

composer install
cp .env.example .env
php artisan key:generate

# Configure your database in .env, then:
php artisan migrate

php artisan serve
```

## 📡 Main API Endpoints

### Auth
```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/refresh
GET    /api/auth/me
POST   /api/auth/logout
GET    /api/devices
DELETE /api/devices/{deviceId}
POST   /api/devices/revoke-others
```

### Businesses
```
GET    /api/businesses
GET    /api/businesses/{business}/members
POST   /api/businesses/{business}/members
DELETE /api/businesses/{business}/members

GET    /api/businesses/{business}/roles
POST   /api/businesses/{business}/roles
PATCH  /api/businesses/{business}/roles/{role}
DELETE /api/businesses/{business}/roles

GET    /api/businesses/{business}/products
POST   /api/businesses/{business}/products
PATCH  /api/businesses/{business}/products/{product}
DELETE /api/businesses/{business}/products
```

## 🏗️ Architecture

```
app/                     Presentation layer (Controllers, Requests, Resources)
├── Core/                Shared infrastructure (base classes, middlewares, providers)
└── Modules/{Domain}/    HTTP layer per domain (Auth, Business, User, Product)

src/
├── Domain/{Domain}/     Business logic — framework-agnostic
│   ├── Actions/         Use cases
│   ├── Data/            DTOs (Spatie Laravel Data)
│   ├── Models/          Eloquent models
│   └── Interfaces/      Repository contracts
└── Infrastructure/{Domain}/
    └── Repositories/    Concrete repository implementations
```

## 🔒 Security

- Refresh tokens hashed (SHA-256)
- Device fingerprinting & IP tracking
- Rate limiting on auth-sensitive routes
- Automatic device limit enforcement per user
- Token rotation on refresh
- Soft deletes on regulated entities (audit trail)

## 🧪 Testing

```bash
composer test
# or a specific suite
php artisan test --filter=Business
```

## 📚 Documentation

- API docs generated via Scramble at `/docs/api` once the app is running
- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Spatie Laravel Data](https://spatie.be/docs/laravel-data)
- [Pest PHP](https://pestphp.com/)

## 📝 License

MIT
