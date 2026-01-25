<?php

// routes/api/businesses.php

declare(strict_types=1);

use App\Features\Business\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

Route::prefix('businesses')
    ->controller(BusinessController::class)
    ->name('businesses.')
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');
        Route::get('{business}/users', 'businessUsers')
            ->name('business.users');
        Route::post('{business}/invite', 'inviteUser')
            ->name('business.invite');
    });
