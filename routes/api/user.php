<?php

// routes/api.php

declare(strict_types=1);

use App\Modules\User\Controllers\UserBaseController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')
    ->controller(UserBaseController::class)
    ->name('user.')
    ->group(function () {
        Route::get('user', 'user')
            ->name('user');
    });
