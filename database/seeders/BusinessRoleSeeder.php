<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessRole;
use Illuminate\Database\Seeder;

class BusinessRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create restaurant-specific custom roles for each restaurant
     */
    public function run(): void
    {
        $businesses = Business::all();

        // Default roles that every restaurant should have
        $defaultRoles = [
            [
                'name' => 'gerant',
                'display_name' => 'Gérant',
                'description' => 'Responsable du restaurant, accès complet',
                'color' => 'FF5733',
            ],
            [
                'name' => 'chef_cuisine',
                'display_name' => 'Chef de Cuisine',
                'description' => 'Responsable de la cuisine',
                'color' => '33FF57',
            ],
            [
                'name' => 'serveur',
                'display_name' => 'Serveur',
                'description' => 'Service en salle',
                'color' => '3357FF',
            ],
            [
                'name' => 'cuisinier',
                'display_name' => 'Cuisinier',
                'description' => 'Préparation des plats',
                'color' => 'FF33A8',
            ],
            [
                'name' => 'plongeur',
                'display_name' => 'Plongeur',
                'description' => 'Nettoyage et plonge',
                'color' => 'A833FF',
            ],
        ];

        foreach ($businesses as $business) {
            foreach ($defaultRoles as $roleData) {
                $role = new BusinessRole;
                $role->business_id = $business->id;
                $role->name = $roleData['name'];
                $role->display_name = $roleData['display_name'];
                $role->description = $roleData['description'];
                $role->color = $roleData['color'];
                $role->save();
            }
        }
    }
}
