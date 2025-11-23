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
        // 1. System roles and permissions (must be first)
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);

        // 2. Users (need roles to exist)
        $this->call(UserSeeder::class);

        // 3. Restaurant types and restaurants
        $this->call(RestaurantTypeSeeder::class);
        $this->call(RestaurantSeeder::class);

        // 4. Restaurant-specific roles and permissions
        $this->call(RestaurantRoleSeeder::class);
        $this->call(RestaurantRolePermissionSeeder::class);

        // 5. Link users to restaurants (must be last)
        $this->call(RestaurantUserSeeder::class);
    }

    public function production()
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(RestaurantTypeSeeder::class);
    }
}
