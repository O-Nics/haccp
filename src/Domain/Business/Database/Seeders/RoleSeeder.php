<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'owner', 'display_name' => 'Propriétaire', 'color' => 'dc2626', 'is_system' => true],
            ['name' => 'manager', 'display_name' => 'Manager', 'color' => 'ea580c', 'is_system' => false],
            ['name' => 'employee', 'display_name' => 'Employé', 'color' => '16a34a', 'is_system' => false],
            ['name' => 'viewer', 'display_name' => 'Lecteur', 'color' => '2563eb', 'is_system' => false],
        ];

        $businesses = Business::all();

        foreach ($businesses as $business) {
            foreach ($roles as $role) {
                Role::query()->updateOrCreate(
                    [
                        'business_id' => $business->id,
                        'name' => $role['name'],
                    ],
                    array_merge($role, ['business_id' => $business->id])
                );
            }
        }
    }
}
