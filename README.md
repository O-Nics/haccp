# 🚀 Laravel 12 Auth Starter

Production-ready authentication API built with **Laravel 12** and **PHP 8.4**.

## ✨ Features

- ✅ **Multi-device support** with UUIDs v7 (better DB indexing)
- ✅ **Access + Refresh tokens** (with automatic rotation)
- ✅ **Rate limiting** (5 attempts per 15 min)
- ✅ **Device fingerprinting** (SHA-256)
- ✅ **Carbon 3** (latest)
- ✅ **PHP 8.4** (all modern features)
- ✅ **Type-safe** (strict types, enums, DTOs, readonly classes)
- ✅ **Fully tested** (PHPUnit)

## 📋 Requirements

- PHP 8.3 or 8.4
- Laravel 12
- MySQL 8.0+ / PostgreSQL 14+ / SQLite 3.35+
- Redis 6.0+ (recommended for caching)

## 🚀 Installation
```bash
# Clone
git clone https://github.com/onicks/laravel-auth.git
cd laravel-auth-starter

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=mysql
# DB_DATABASE=laravel_auth
# ...

# Run migrations
php artisan migrate

# Serve
php artisan serve
```

## 📡 API Endpoints

### Register
```bash
POST /api/auth/register
Content-Type: application/json

{
  "name": "Nicolas",
  "email": "nicolas@example.com",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!",
  "device_name": "iPhone 14"
}
```

### Login
```bash
POST /api/auth/login
Content-Type: application/json

{
  "email": "nicolas@example.com",
  "password": "SecurePass123!",
  "device_name": "MacBook Pro",
  "remember_me": false
}
```

### Refresh Token
```bash
POST /api/auth/refresh
Content-Type: application/json

{
  "refresh_token": "...",
  "device_id": "uuid"
}
```

### Me
```bash
GET /api/auth/me
Authorization: Bearer {access_token}
```

### Logout
```bash
POST /api/auth/logout
Authorization: Bearer {access_token}
Content-Type: application/json

{
  "device_id": "uuid"
}
```

### List Devices
```bash
GET /api/devices
Authorization: Bearer {access_token}
```

### Revoke Device
```bash
DELETE /api/devices/{deviceId}
Authorization: Bearer {access_token}
```

### Revoke All Other Devices
```bash
POST /api/devices/revoke-others
Authorization: Bearer {access_token}
Content-Type: application/json

{
  "current_device_id": "uuid"
}
```

## 🎯 Laravel 12 Features Used

### UUIDs v7
Better database performance with time-sortable UUIDs.

### Carbon 3
Latest Carbon version with improved performance.

### xxHash
Laravel 12 uses xxHash internally for better hashing performance.

### Per-second Rate Limiting
More granular rate limiting control.

## 🧪 Testing
```bash
php artisan test
```

## 🔒 Security

- ✅ Refresh tokens hashed with SHA-256
- ✅ Device fingerprinting
- ✅ Rate limiting (brute-force protection)
- ✅ IP tracking
- ✅ Automatic device limit enforcement
- ✅ Token rotation on refresh

## 📈 Performance Optimizations

- ✅ UUIDs v7 for better DB indexing
- ✅ Optimized database indexes
- ✅ Redis caching ready
- ✅ Eager loading relationships
- ✅ Query optimization

## 🛠️ Configuration

Edit `config/auth.php`:
```php
'tokens' => [
    'access' => [
        'expiration' => 15, // minutes
    ],
    'refresh' => [
        'expiration' => 43200, // 30 days
        'rotation' => true,
    ],
],
'devices' => [
    'max_per_user' => 5,
],
```

## 📚 Documentation

- [Laravel 12 Docs](https://laravel.com/docs/12.x)
- [Sanctum Docs](https://laravel.com/docs/12.x/sanctum)

## 📝 License

MIT License

---

**Built with ❤️ using Laravel 12 + PHP 8.4**
