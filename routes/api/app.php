<?php

declare(strict_types=1);

use App\Modules\App\Controllers\LoadInitialDataController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'status' => 'ok',
    'timestamp' => now()->toIso8601String(),
]))->name('health');

Route::get('app/init', LoadInitialDataController::class)->name('app.init');
