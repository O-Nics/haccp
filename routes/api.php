<?php

// routes/api.php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

require __DIR__ . '/api/app.php';
require __DIR__.'/api/auth.php';
require __DIR__.'/api/password.php';

Route::middleware('auth:sanctum')->group(function () {
    require __DIR__.'/api/user.php';
    require __DIR__ . '/api/business.php';
});
