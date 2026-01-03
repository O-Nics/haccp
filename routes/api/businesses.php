<?php

// routes/api/businesses.php

declare(strict_types=1);

use App\Http\Controllers\Api\Business\BusinessController;
use Illuminate\Support\Facades\Route;

Route::prefix('businesses')
    ->name('businesses.')
    ->group(function () {
        Route::get('/', [BusinessController::class, 'index'])
            ->name('index');

        Route::prefix('{business}')
            ->name('business.')
            ->group(function () {
                // add routes/api/business_user.php to routes/api/businesses.php
                require __DIR__ . '/business_users.php';
            });
    });
