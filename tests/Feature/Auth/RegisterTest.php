<?php

namespace Tests\Feature\Auth;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Tests\TestHelpers\DeviceHelper;

use function Pest\Laravel\postJson;

pest()->group('auth');

uses(DeviceHelper::class);

