<?php

// routes/api.php

declare(strict_types=1);

use App\Features\Device\Controllers\DeviceController;
use Illuminate\Support\Facades\Route;

Route::prefix('devices')
    ->name('devices.')
    ->group(function () {
        Route::get('/', [DeviceController::class, 'index'])
            ->name('index');
        Route::delete('/{deviceId}', [DeviceController::class, 'destroy'])
            ->name('destroy');
        Route::post('/revoke-others', [DeviceController::class, 'revokeOthers'])
            ->name('revoke-others');
    });
