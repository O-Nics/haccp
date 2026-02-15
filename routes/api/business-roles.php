<?php

// routes/api/business-roles.php

declare(strict_types=1);

use App\Modules\Business\Controllers\BusinessRoleBaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('business-roles')
    ->controller(BusinessRoleBaseController::class)
    ->name('business-roles.')
    ->group(function () {
        Route::get('{business}', 'index')
            ->name('index');
    });
