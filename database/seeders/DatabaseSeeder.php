<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Auth\Database\Seeders\DeviceSeeder;
use Domain\Auth\Database\Seeders\SessionSeeder;
use Domain\Auth\Database\Seeders\UserSeeder;
use Domain\Business\Database\Seeders\BusinessSeeder;
use Domain\Business\Database\Seeders\BusinessTypeSeeder;
use Domain\Business\Database\Seeders\MemberSeeder;
use Domain\Business\Database\Seeders\OrganisationSeeder;
use Domain\Business\Database\Seeders\PermissionSeeder;
use Domain\Business\Database\Seeders\RolePermissionSeeder;
use Domain\Business\Database\Seeders\RoleSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->initialize();

        if (app()->environment(['testing', 'local'])) {
            $this->callTesting();
        }
    }

    private function initialize(): void
    {
        $this->call([
            PermissionSeeder::class,
            BusinessTypeSeeder::class,
        ]);
    }

    private function callTesting(): void
    {
        $this->call([
            UserSeeder::class,
            DeviceSeeder::class,
            SessionSeeder::class,
            OrganisationSeeder::class,
            BusinessSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            MemberSeeder::class,
        ]);
    }
}
