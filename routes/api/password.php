<?php

declare(strict_types=1);

use App\Features\Auth\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

Route::prefix('password')
    ->controller(PasswordController::class)
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
