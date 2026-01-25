<?php

declare(strict_types=1);

use App\Features\BusinessUser\Controllers\BusinessUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('businesses-users')
    ->controller(BusinessUserController::class)
    ->name('businesses.users.')
    ->group(function () {
        Route::get('{business}', 'index')
            ->name('index');
        Route::post('{business}', 'create')
            ->name('create');
    });
