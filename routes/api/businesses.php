<?php

// routes/api/businesses.php

declare(strict_types=1);

use App\Features\Business\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

Route::prefix('businesses')
    ->name('businesses.')
    ->group(function () {
        Route::get('/', [BusinessController::class, 'index'])
            ->name('index');

    });
