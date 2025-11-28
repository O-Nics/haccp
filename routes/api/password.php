<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\PasswordController;
use App\Http\Controllers\Api\Auth\RefreshController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Middleware\ThrottleAuth;
use Illuminate\Support\Facades\Route;

Route::prefix('password')
    ->name('password.')
    ->group(function () {
        Route::post('forgot', [PasswordController::class, 'forgot'])
            ->name('forgot');
        Route::post('confirm', [PasswordController::class, 'confirm'])
            ->name('confirm');
        Route::post('reset', [PasswordController::class, 'reset'])
            ->name('reset');
    }
);
