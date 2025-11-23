<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create predefined roles
        $permissions = [
            [
                'name' => 'user.view_any',
                'display_name' => 'Voir les utilisateurs',
                'description' => 'Permet de voir la liste des utilisateurs.',
            ],
            [
                'name' => 'user.view',
                'display_name' => 'Voir un utilisateur',
                'description' => 'Permet de voir les détails d\'un utilisateur spécifique.',
            ],
            [
                'name' => 'user.create',
                'display_name' => 'Créer un utilisateur',
                'description' => 'Permet de créer un nouvel utilisateur.',
            ],
            [
                'name' => 'user.update',
                'display_name' => 'Mettre à jour un utilisateur',
                'description' => 'Permet de modifier les informations d\'un utilisateur existant.',
            ],
            [
                'name' => 'user.delete',
                'display_name' => 'Supprimer un utilisateur',
                'description' => 'Permet de supprimer un utilisateur.',
            ],

        ];
        foreach ($permissions as $permissionData) {
            if (! Permission::where('name', $permissionData['name'])->exists()) {
                Permission::create($permissionData);
            }
        }
    }
}
