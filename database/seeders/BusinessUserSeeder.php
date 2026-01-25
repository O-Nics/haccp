<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@haccp.local')->first();
        $jean = User::query()->where('email', 'jean@haccp.local')->first();
        $marie = User::query()->where('email', 'marie@haccp.local')->first();
        $pierre = User::query()->where('email', 'pierre@haccp.local')->first();
        $sophie = User::query()->where('email', 'sophie@haccp.local')->first();

        $gourmet = Business::query()->where('email', 'gourmet@haccp-solutions.fr')->first();
        $miedoree = Business::query()->where('email', 'miedoree@haccp-solutions.fr')->first();
        $traiteur = Business::query()->where('email', 'traiteur@delices-jean.fr')->first();

        if (! $gourmet || ! $miedoree || ! $traiteur) {
            return;
        }

        $ownerGourmet = $gourmet->businessRoles()->where('name', 'owner')->first();
        $managerGourmet = $gourmet->businessRoles()->where('name', 'manager')->first();
        $employeeGourmet = $gourmet->businessRoles()->where('name', 'employee')->first();

        $ownerMiedoree = $miedoree->businessRoles()->where('name', 'owner')->first();
        $employeeMiedoree = $miedoree->businessRoles()->where('name', 'employee')->first();

        $ownerTraiteur = $traiteur->businessRoles()->where('name', 'owner')->first();
        $employeeTraiteur = $traiteur->businessRoles()->where('name', 'employee')->first();

        $businessUsers = [
            // Le Gourmet Parisien
            [
                'business_id' => $gourmet->id,
                'user_id' => $admin?->id,
                'role_id' => $ownerGourmet?->id,
                'status' => 'active',
                'joined_at' => now()->subMonths(6),
            ],
            [
                'business_id' => $gourmet->id,
                'user_id' => $marie?->id,
                'role_id' => $managerGourmet?->id,
                'invited_by' => $admin?->id,
                'status' => 'active',
                'invited_at' => now()->subMonths(3),
                'joined_at' => now()->subMonths(3),
            ],
            [
                'business_id' => $gourmet->id,
                'user_id' => $pierre?->id,
                'role_id' => $employeeGourmet?->id,
                'invited_by' => $marie?->id,
                'status' => 'active',
                'invited_at' => now()->subMonth(),
                'joined_at' => now()->subMonth(),
            ],

            // La Mie Dorée
            [
                'business_id' => $miedoree->id,
                'user_id' => $admin?->id,
                'role_id' => $ownerMiedoree?->id,
                'status' => 'active',
                'joined_at' => now()->subMonths(4),
            ],
            [
                'business_id' => $miedoree->id,
                'user_id' => $sophie?->id,
                'role_id' => $employeeMiedoree?->id,
                'invited_by' => $admin?->id,
                'status' => 'active',
                'invited_at' => now()->subWeeks(2),
                'joined_at' => now()->subWeek(),
            ],

            // Traiteur Jean
            [
                'business_id' => $traiteur->id,
                'user_id' => $jean?->id,
                'role_id' => $ownerTraiteur?->id,
                'status' => 'active',
                'joined_at' => now()->subMonths(2),
            ],
            [
                'business_id' => $traiteur->id,
                'user_id' => $pierre?->id,
                'role_id' => $employeeTraiteur?->id,
                'invited_by' => $jean?->id,
                'status' => 'invited',
                'invited_at' => now()->subDays(3),
            ],
        ];

        foreach ($businessUsers as $bu) {
            if ($bu['user_id'] && $bu['role_id']) {
                BusinessUser::query()->updateOrCreate(
                    [
                        'business_id' => $bu['business_id'],
                        'user_id' => $bu['user_id'],
                    ],
                    $bu
                );
            }
        }
    }
}
