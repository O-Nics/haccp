<?php

declare(strict_types=1);

use App\Features\App\Controllers\LoadInitialDataController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'timestamp' => now()->toIso8601String(),
]))->name('health');

Route::prefix('load-initial-data')
     ->name('load-initial-data.')
     ->group(function () {
         Route::post('/', LoadInitialDataController::class)->name('load');
     });

