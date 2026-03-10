<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Auth\Database\Seeders\DeviceSeeder;
use Domain\Auth\Database\Seeders\UserSeeder;
use Domain\Business\Database\Seeders\BusinessSeeder;
use Domain\Business\Database\Seeders\BusinessTypeSeeder;
use Domain\Business\Database\Seeders\MemberSeeder;
use Domain\Business\Database\Seeders\OrganisationSeeder;
use Domain\Business\Database\Seeders\PermissionSeeder;
use Domain\Business\Database\Seeders\RolePermissionSeeder;
use Domain\Business\Database\Seeders\RoleSeeder;
use Domain\Product\Database\Seeders\ProductSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeds the database for the current environment.
     * initialize() runs in all environments (required reference data).
     * callTesting() runs only in local and testing environments (dev/test fixtures).
     *
     * @return void
     */
    public function run(): void
    {
        $this->initialize();

        if (app()->environment(['testing', 'local'])) {
            $this->callTesting();
        }
    }

    /**
     * Seeds required reference data that must exist in all environments.
     * These seeders are safe to re-run (all use updateOrCreate).
     *
     * @return void
     */
    private function initialize(): void
    {
        $this->call([
            PermissionSeeder::class,
            BusinessTypeSeeder::class,
        ]);
    }

    /**
     * Seeds realistic fixtures for development and testing environments.
     * Order is mandatory — each seeder depends on the previous ones.
     *
     *  1. UserSeeder         → 4 fixed users (alice, bob, charlie, diana) + 8 random
     *  2. DeviceSeeder       → 1 device per fixed user + 1 per extra user
     *  3. OrganisationSeeder → HACCP Solutions (alice) + Les Délices de Diana (diana)
     *  4. BusinessSeeder     → Le Gourmet, La Mie Dorée (org HACCP) + Traiteur (org Délices)
     *  5. RoleSeeder         → 4 system roles (owner/manager/employee/viewer) per business
     *  6. RolePermissionSeeder → permissions synced per role type
     *  7. MemberSeeder       → fixed scenarios + extra filler members
     *  8. ProductSeeder      → 10 products per business
     *
     * @return void
     */
    private function callTesting(): void
    {
        $this->call([
            UserSeeder::class,
            DeviceSeeder::class,
            OrganisationSeeder::class,
            BusinessSeeder::class,
            RoleSeeder::class,
            RolePermissionSeeder::class,
            MemberSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
