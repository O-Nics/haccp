<?php

// routes/api/businesses.php

declare(strict_types=1);

use App\Features\Permission\Controllers\PermissionsController;
use Illuminate\Support\Facades\Route;

Route::prefix('permissions')
    ->controller(PermissionsController::class)
    ->name('permissions.')
    ->group(function () {
        Route::get('', 'index')
            ->name('index');
    });
