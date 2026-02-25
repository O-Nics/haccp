<?php

declare(strict_types=1);

use App\Modules\Business\Controllers\BusinessController;
use App\Modules\Business\Controllers\MemberController;
use App\Modules\Business\Controllers\RoleController;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;
use Illuminate\Support\Facades\Route;

Route::prefix('businesses')
    ->name('businesses.')
    ->group(function () {
        // Businesses
        Route::controller(BusinessController::class)
            ->group(function () {
                Route::get('', 'index')->name('index');
            });

        Route::prefix('{business}')
            ->group(function () {
                // Members
                Route::prefix('members')
                    ->controller(MemberController::class)
                    ->name('members.')
                    ->group(function () {
                        Route::get('', 'index')->name('index')
                            ->can('view-any', [Member::class, 'business']);
                        Route::post('', 'store')->name('store')
                            ->can('create', [Member::class, 'business']);
                    });

                // Roles
                Route::prefix('roles')
                    ->controller(RoleController::class)
                    ->name('roles.')
                    ->group(function () {
                        Route::get('', 'index')->name('index')
                            ->can('view-any', [Role::class, 'business']);
                        Route::get('', 'store')->name('store')
                            ->can('view-any', [Role::class, 'business']);
                    });
            });
    });
