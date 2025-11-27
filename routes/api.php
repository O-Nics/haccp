<?php

// routes/api.php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

require __DIR__.'/api/health.php';
require __DIR__.'/api/auth.php';

Route::middleware('auth:sanctum')->group(function () {
    require __DIR__.'/api/devices.php';
    require __DIR__.'/api/user.php';
});
