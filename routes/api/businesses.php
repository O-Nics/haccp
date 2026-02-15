<?php

declare(strict_types=1);

use App\Modules\Business\Controllers\BusinessController;
use App\Modules\Business\Controllers\MemberController;
use App\Modules\Business\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Businesses
Route::prefix('businesses')
    ->controller(BusinessController::class)
    ->name('businesses.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });

// Members
Route::prefix('members')
    ->controller(MemberController::class)
    ->name('members.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });

// Roles
Route::prefix('roles')
    ->controller(RoleController::class)
    ->name('roles.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
    });
