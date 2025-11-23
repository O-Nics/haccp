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

// Health check (Laravel 12 feature)
Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'timestamp' => now()->toIso8601String(),
]))->name('health');

// Auth routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('register', RegisterController::class)
        ->name('register');

    Route::post('login', LoginController::class)
        ->middleware(ThrottleAuth::class)
        ->name('login');

    Route::post('refresh', RefreshController::class)
        ->name('refresh');

    // Password reset routes
    Route::post('password/forgot', [PasswordController::class, 'forgot'])
        ->name('password.forgot');
    Route::post('password/confirm', [PasswordController::class, 'confirm'])
        ->name('password.confirm');
    Route::post('password/reset', [PasswordController::class, 'reset'])
        ->name('password.reset');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', LogoutController::class)
            ->name('logout');
        Route::get('me', [UserController::class, 'user'])
            ->name('me');
    });
});

// Device management
Route::middleware('auth:sanctum')
    ->prefix('devices')
    ->name('devices.')
    ->group(function () {
        Route::get('/', [DeviceController::class, 'index'])
            ->name('index');
        Route::delete('/{deviceId}', [DeviceController::class, 'destroy'])
            ->name('destroy');
        Route::post('/revoke-others', [DeviceController::class, 'revokeOthers'])
            ->name('revoke-others');
    });
