<?php

declare(strict_types=1);

namespace Domain\Auth\Database\Seeders;

use Domain\Auth\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create()->count(15);
    }
}
