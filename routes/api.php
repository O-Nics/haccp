<?php

// routes/api.php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordController;
use App\Http\Controllers\Api\Auth\RefreshController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Device\DeviceController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Middleware\ThrottleAuth;
use Illuminate\Support\Facades\Route;


require __DIR__.'/api/health.php';
require __DIR__ . '/api/auth.php';

Route::middleware('auth:sanctum')->group(function () {
    require __DIR__ . '/api/devices.php';
    require __DIR__ . '/api/user.php';
});

