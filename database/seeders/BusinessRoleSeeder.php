<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Organisation;
use App\Models\OrganisationRole;
use Illuminate\Database\Seeder;

class BusinessRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Create organisation-specific custom roles for each organisation
     */
    public function run(): void
    {
        $organisations = Organisation::all();

        // Default roles that every organisation should have
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

        foreach ($organisations as $organisation) {
            foreach ($defaultRoles as $roleData) {
                $role = new OrganisationRole;
                $role->organisation_id = $organisation->id;
                $role->name = $roleData['name'];
                $role->display_name = $roleData['display_name'];
                $role->description = $roleData['description'];
                $role->color = $roleData['color'];
                $role->save();
            }
        }
    }
}
