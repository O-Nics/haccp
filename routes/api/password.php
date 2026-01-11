<?php

declare(strict_types=1);

use App\Features\Auth\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('password')
    ->name('password.')
    ->group(function () {
        Route::post('forgot', [PasswordController::class, 'forgot'])
            ->name('forgot');
        Route::post('confirm', [PasswordController::class, 'checkReset'])
            ->name('confirm');
        Route::post('reset', [PasswordController::class, 'reset'])
            ->name('reset');
    }
);
