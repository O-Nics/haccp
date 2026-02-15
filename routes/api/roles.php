<?php

declare(strict_types=1);

use App\Modules\Business\Controllers\BusinessController;
use App\Modules\Business\Controllers\RoleController;
use App\Modules\Business\Controllers\BusinessUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')
    ->controller(RoleController::class)
    ->name('roles.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });
