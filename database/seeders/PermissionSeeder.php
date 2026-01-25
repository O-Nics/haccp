<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Utilisateurs
            ['name' => 'users.view', 'display_name' => 'Voir les utilisateurs'],
            ['name' => 'users.create', 'display_name' => 'Créer des utilisateurs'],
            ['name' => 'users.edit', 'display_name' => 'Modifier les utilisateurs'],
            ['name' => 'users.delete', 'display_name' => 'Supprimer les utilisateurs'],

            // Rôles
            ['name' => 'roles.view', 'display_name' => 'Voir les rôles'],
            ['name' => 'roles.create', 'display_name' => 'Créer des rôles'],
            ['name' => 'roles.edit', 'display_name' => 'Modifier les rôles'],
            ['name' => 'roles.delete', 'display_name' => 'Supprimer les rôles'],

            // Business
            ['name' => 'business.view', 'display_name' => 'Voir le business'],
            ['name' => 'business.edit', 'display_name' => 'Modifier le business'],

            // HACCP
            ['name' => 'haccp.view', 'display_name' => 'Voir les contrôles HACCP'],
            ['name' => 'haccp.create', 'display_name' => 'Créer des contrôles HACCP'],
            ['name' => 'haccp.edit', 'display_name' => 'Modifier les contrôles HACCP'],
            ['name' => 'haccp.delete', 'display_name' => 'Supprimer les contrôles HACCP'],
            ['name' => 'haccp.validate', 'display_name' => 'Valider les contrôles HACCP'],

            // Rapports
            ['name' => 'reports.view', 'display_name' => 'Voir les rapports'],
            ['name' => 'reports.export', 'display_name' => 'Exporter les rapports'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
