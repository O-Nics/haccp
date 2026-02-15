<?php

// routes/api/businesses.php

declare(strict_types=1);

use App\Modules\Business\Controllers\BusinessBaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('businesses')
    ->controller(BusinessBaseController::class)
    ->name('businesses.')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');
    });
