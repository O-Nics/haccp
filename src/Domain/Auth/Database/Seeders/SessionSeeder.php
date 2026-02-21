<?php

declare(strict_types=1);

namespace Domain\Auth\Database\Seeders;

use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        Session::factory()
            ->count(15)
            ->withUser()
            ->withDevice()
            ->create();    }
}
