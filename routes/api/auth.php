<?php

declare(strict_types=1);

use App\Core\Middlewares\ThrottleAuth;
use App\Modules\Auth\Controllers\AuthController;
use App\Modules\Auth\Controllers\PasswordController;
use Domain\Auth\Enums\TokenAbilityEnum;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->name('auth.')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('register', 'register')
            ->middleware(ThrottleAuth::class.':register,5,15')
            ->name('register');

        Route::post('login', 'login')
            ->middleware(ThrottleAuth::class.':login')
            ->name('login');

        Route::middleware(['auth:sanctum', 'ability:'.TokenAbilityEnum::REFRESH->value])->group(function () {
            Route::post('refresh', 'refresh')->name('refresh');
        });

        Route::middleware(['auth:sanctum', 'ability:'.TokenAbilityEnum::ACCESS->value])->group(function () {
            Route::post('logout', 'logout')
                ->name('logout');
            Route::post('check-auth', 'checkAuth')
                ->name('check-auth');
        });

        Route::prefix('password')
            ->controller(PasswordController::class)
            ->name('password.')
            ->middleware(ThrottleAuth::class.':password,5,15')
            ->group(function () {
                Route::post('forgot', 'forgot')
                    ->name('forgot');
                Route::post('confirm', 'checkReset')
                    ->name('confirm');
                Route::post('reset', 'reset')
                    ->name('reset');
            });
    });
