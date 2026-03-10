<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Domain\Business\Enums\RoleNameEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * System roles seeded into every business.
     * The `is_system` flag marks roles that cannot be deleted by users.
     */
    private array $roles = [
        [
            'name'         => RoleNameEnum::OWNER,
            'display_name' => 'Propriétaire',
            'color'        => 'dc2626',
            'is_system'    => true,
        ],
        [
            'name'         => RoleNameEnum::MANAGER,
            'display_name' => 'Manager',
            'color'        => 'ea580c',
            'is_system'    => false,
        ],
        [
            'name'         => RoleNameEnum::EMPLOYEE,
            'display_name' => 'Employé',
            'color'        => '16a34a',
            'is_system'    => false,
        ],
        [
            'name'         => RoleNameEnum::VIEWER,
            'display_name' => 'Lecteur',
            'color'        => '2563eb',
            'is_system'    => false,
        ],
    ];

    /**
     * Seeds the 4 system roles for every existing business.
     * Identified by (business_id, name) for idempotent re-seeding.
     * New businesses added later will receive their roles on the next seed run.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var Collection<int, Business> $businesses */
        $businesses = Business::all();

        foreach ($businesses as $business) {
            foreach ($this->roles as $roleData) {
                $business->roles()->updateOrCreate(
                    ['name' => $roleData['name']->value],
                    [
                        'name'         => $roleData['name']->value,
                        'display_name' => $roleData['display_name'],
                        'color'        => $roleData['color'],
                        'is_system'    => $roleData['is_system'],
                    ]
                );
            }
        }
    }
}
