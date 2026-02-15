<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Auth\Database\Seeders\UserSeeder;
use Domain\Business\Database\Seeders\BusinessSeeder;
use Domain\Business\Database\Seeders\BusinessTypeSeeder;
use Domain\Business\Database\Seeders\BusinessUserSeeder;
use Domain\Business\Database\Seeders\OrganisationSeeder;
use Domain\Business\Database\Seeders\PermissionSeeder;
use Domain\Business\Database\Seeders\RolePermissionSeeder;
use Domain\Business\Database\Seeders\RoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            BusinessTypeSeeder::class,
            UserSeeder::class,
            OrganisationSeeder::class,
            BusinessSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            BusinessUserSeeder::class,
        ]);
    }
}
