<?php

declare(strict_types=1);

use App\Modules\Business\Controllers\BusinessController;
use App\Modules\Business\Controllers\MemberController;
use App\Modules\Business\Controllers\RoleController;
use App\Modules\Product\Controllers\ProductController;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;
use Domain\Product\Models\Product;
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
                        Route::delete('', 'deleteMany')->name('delete')
                            ->can('delete-many', [Member::class, 'business']);
                    });

                // Roles
                Route::prefix('roles')
                    ->controller(RoleController::class)
                    ->name('roles.')
                    ->group(function () {
                        Route::get('', 'index')->name('index')
                            ->can('view-any', [Role::class, 'business']);
                        Route::post('', 'store')->name('store')
                            ->can('create', [Role::class, 'business']);
                        Route::patch('{role}', 'update')->name('update')
                            ->can('update', [Role::class, 'business']);
                        Route::delete('', 'deleteMany')->name('delete')
                            ->can('delete-many', [Role::class, 'business']);
                    });

                // Products
                Route::prefix('products')
                    ->controller(ProductController::class)
                    ->name('products.')
                    ->group(function () {
                        Route::get('', 'index')->name('index')
                            ->can('view-any', [Product::class, 'business']);
                        Route::post('', 'store')->name('store')
                            ->can('create', [Product::class, 'business']);
                        Route::patch('{product}', 'update')->name('update')
                            ->can('update', [Product::class, 'business']);
                        Route::delete('', 'deleteMany')->name('delete')
                            ->can('delete-many', [Product::class, 'business']);
                    });
            });
    });
