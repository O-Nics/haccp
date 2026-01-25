<?php

// routes/api/businesses-users.php

declare(strict_types=1);

use App\Features\Business\Controllers\BusinessUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('businesses-users')
    ->controller(BusinessUserController::class)
    ->name('businesses.users.')
    ->group(function () {
        Route::get('{business}', 'index')
            ->name('index');
        Route::post('{business}', 'create')
            ->name('business.users');
    });
