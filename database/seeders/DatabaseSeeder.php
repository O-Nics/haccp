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
        //
        if (app()->environment('production')) {
            $this->production();
        }
    }

    public function local()
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(BusinessTypeSeeder::class);
        $this->call(BusinessSeeder::class);
        $this->call(BusinessRoleSeeder::class);
        $this->call(BusinessRolePermissionSeeder::class);
        $this->call(BusinessUserSeeder::class);
        $this->call(OrganisationSeeder::class);

    }

    public function production()
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(BusinessTypeSeeder::class);
    }
}
