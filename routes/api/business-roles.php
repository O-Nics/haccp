<?php

// routes/api/business-roles.php

declare(strict_types=1);

use App\Features\BusinessRole\Controllers\BusinessRoleController;
use Illuminate\Support\Facades\Route;

Route::prefix('business-roles')
    ->controller(BusinessRoleController::class)
    ->name('business-roles.')
    ->group(function () {
        Route::get('{business}', 'index')
            ->name('index');
    });
