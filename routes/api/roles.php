<?php

// routes/api/roles.php

declare(strict_types=1);

use App\Features\Business\Controllers\BusinessController;
use App\Features\Role\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')
    ->controller(RoleController::class)
    ->name('roles.')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');
    });
