<?php

// routes/api.php

declare(strict_types=1);

use App\Modules\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')
    ->controller(UserController::class)
    ->name('user.')
    ->group(function () {
        Route::get('/', 'show')
            ->name('show');
    });
