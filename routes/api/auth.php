<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\CheckAuthController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RefreshController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Middleware\ThrottleAuth;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->name('auth.')
    ->group(function () {
        Route::post('register', RegisterController::class)
            ->name('register');

        Route::post('login', LoginController::class)
            ->middleware(ThrottleAuth::class)
            ->name('login');

        Route::post('refresh', RefreshController::class)
            ->name('refresh');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('check-auth', CheckAuthController::class)
                ->name('check-auth');
        });

    });
