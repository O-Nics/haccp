<?php

// routes/api.php

declare(strict_types=1);

use App\Features\Auth\Enums\TokenAbilityEnum;
use Illuminate\Support\Facades\Route;

require __DIR__.'/api/app.php';
require __DIR__.'/api/auth.php';

Route::middleware(['auth:sanctum', 'ability:'.TokenAbilityEnum::ACCESS->value])->group(function () {
    require __DIR__.'/api/user.php';
    require __DIR__.'/api/business.php';
});
