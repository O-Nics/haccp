<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class DatabaseSeeder extends Seeder
{
    use WithFaker;
    use WithoutModelEvents;

    public function run(): void
    {
        if (app()->environment('local')) {
            $this->local();
        }

        if (app()->environment('production')) {
            $this->production();
        }
    }

    public function local()
    {
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(RestaurantRolePermissionSeeder::class);
        $this->call(RestaurantTypeSeeder::class);
        $this->call(RestaurantSeeder::class);
    }

    public function production()
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(RestaurantTypeSeeder::class);
    }
}
