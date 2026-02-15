<?php

declare(strict_types=1);

use App\Core\Middlewares\ThrottleAuth;
use App\Modules\Auth\Controllers\AuthBaseController;
use App\Modules\Auth\Controllers\PasswordBaseController;
use Domain\Auth\Enums\TokenAbilityEnum;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->name('auth.')
    ->controller(AuthBaseController::class)
    ->group(function () {
        Route::post('register', 'register')->name('register');

        Route::post('login', 'login')
            ->middleware(ThrottleAuth::class)
            ->name('login');

        Route::middleware(['auth:sanctum', 'ability:'.TokenAbilityEnum::REFRESH->value])->group(function () {
            Route::post('refresh', [AuthBaseController::class, 'refresh']);
        });

        Route::middleware(['auth:sanctum', 'ability:'.TokenAbilityEnum::ACCESS->value])->group(function () {
            Route::post('logout', 'logout')
                ->name('logout');
            Route::post('check-auth', 'checkAuth')
                ->name('check-auth');
        });

        Route::prefix('password')
            ->controller(PasswordBaseController::class)
            ->name('password.')
            ->group(function () {
                Route::post('forgot', 'forgot')
                    ->name('forgot');
                Route::post('confirm', 'checkReset')
                    ->name('confirm');
                Route::post('reset', 'reset')
                    ->name('reset');
            }
            );
    });
