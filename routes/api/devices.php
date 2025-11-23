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

Route::prefix('devices')
    ->name('devices.')
    ->group(function () {
        Route::get('/', [DeviceController::class, 'index'])
            ->name('index');
        Route::delete('/{deviceId}', [DeviceController::class, 'destroy'])
            ->name('destroy');
        Route::post('/revoke-others', [DeviceController::class, 'revokeOthers'])
            ->name('revoke-others');
    });
