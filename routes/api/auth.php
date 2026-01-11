<?php

declare(strict_types=1);

use App\Core\Middlewares\ThrottleAuth;
use App\Features\Auth\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->name('auth.')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('register','register')->name('register');

        Route::post('login','login')
            ->middleware(ThrottleAuth::class)
            ->name('login');

        Route::post('refresh','refresh')
            ->name('refresh');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout','logout')
                ->name('logout');
            Route::post('check-auth', 'checkAuth')
                ->name('check-auth');
        });

    });


