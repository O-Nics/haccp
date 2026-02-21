<?php

declare(strict_types=1);

namespace Domain\Auth\Database\Seeders;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        Device::factory()->create()->count(15);
    }
}
