<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined roles
        $roles = [
            [
                'name' => 'owner',
                'display_name' => 'Proprietaire',
                'description' => 'À plein pouvoir sur toutes les fonctionnalités et paramètres.',
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Peut gérer les utilisateurs et les paramètres, mais avec des restrictions.',
            ],
            [
                'name' => 'staff',
                'display_name' => 'Personnel',
                'description' => 'Peut accéder aux fonctionnalités de base mais avec des permissions limitées.',
            ],
        ];

        foreach ($roles as $roleData) {
            if (! Role::where('name', $roleData['name'])->exists()) {
                Role::create($roleData);
            }
        }
    }
}
