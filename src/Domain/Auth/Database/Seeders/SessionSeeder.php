<?php

declare(strict_types=1);

namespace Domain\Auth\Database\Seeders;

use Domain\Auth\Models\Session;
use Illuminate\Database\Seeder;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        Session::factory()
            ->count(15)
            ->withUser()
            ->withDevice()
            ->create();
    }
}
