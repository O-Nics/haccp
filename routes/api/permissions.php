<?php

// routes/api/businesses.php

declare(strict_types=1);

use App\Features\Business\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

Route::prefix('permissions')
    ->controller(BusinessController::class)
    ->name('permissions.')
    ->group(function () {
        Route::get('', 'index')
            ->name('index');
    });
