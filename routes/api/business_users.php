<?php

// routes/api.php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\BusinessUser\IndexBusinessUserController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('business-users')
    ->name('business-user.s')
    ->group(function () {
        Route::get('', IndexBusinessUserController::class)
            ->name('index');
    });
