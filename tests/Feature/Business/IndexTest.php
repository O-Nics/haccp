<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;

use function Pest\Laravel\postJson;

pest()->group('auth');


