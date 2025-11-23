<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(25)
            ->createQuietly()
            ->each(function (User $user) {
                Device::factory()
                    ->for($user)
                    ->createQuietly();
            });
    }
}
