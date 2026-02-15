<?php

declare(strict_types=1);

use App\Modules\Business\Controllers\BusinessController;
use App\Modules\Business\Controllers\RoleController;
use App\Modules\Business\Controllers\BusinessUserController;
use Illuminate\Support\Facades\Route;

// Businesses
Route::prefix('businesses')
    ->controller(BusinessController::class)
    ->name('businesses.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });

// Business Users
Route::prefix('business-users')
    ->controller(BusinessUserController::class)
    ->name('business-users.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });
