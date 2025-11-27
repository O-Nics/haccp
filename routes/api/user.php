<?php

// routes/api.php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')
    ->name('user.')
    ->group(function () {
        Route::post('logout', LogoutController::class)
            ->name('logout');
        Route::get('me', [UserController::class, 'user'])
            ->name('me');
    });
