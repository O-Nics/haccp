<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = $permissions = [
            // Utilisateurs
            ['category' => 'users', 'category_label' => 'Utilisateurs', 'can' => 'users.view',   'display_name' => 'Voir les utilisateurs'],
            ['category' => 'users', 'category_label' => 'Utilisateurs', 'can' => 'users.create', 'display_name' => 'Créer des utilisateurs'],
            ['category' => 'users', 'category_label' => 'Utilisateurs', 'can' => 'users.edit',   'display_name' => 'Modifier les utilisateurs'],
            ['category' => 'users', 'category_label' => 'Utilisateurs', 'can' => 'users.delete', 'display_name' => 'Supprimer les utilisateurs'],

            // Rôles
            ['category' => 'roles', 'category_label' => 'Rôles', 'can' => 'roles.view',   'display_name' => 'Voir les rôles'],
            ['category' => 'roles', 'category_label' => 'Rôles', 'can' => 'roles.create', 'display_name' => 'Créer des rôles'],
            ['category' => 'roles', 'category_label' => 'Rôles', 'can' => 'roles.edit',   'display_name' => 'Modifier les rôles'],
            ['category' => 'roles', 'category_label' => 'Rôles', 'can' => 'roles.delete', 'display_name' => 'Supprimer les rôles'],

            // Business
            ['category' => 'business', 'category_label' => 'Business', 'can' => 'business.view', 'display_name' => 'Voir le business'],
            ['category' => 'business', 'category_label' => 'Business', 'can' => 'business.edit', 'display_name' => 'Modifier le business'],

            // HACCP
            ['category' => 'haccp', 'category_label' => 'HACCP', 'can' => 'haccp.view',     'display_name' => 'Voir les contrôles HACCP'],
            ['category' => 'haccp', 'category_label' => 'HACCP', 'can' => 'haccp.create',   'display_name' => 'Créer des contrôles HACCP'],
            ['category' => 'haccp', 'category_label' => 'HACCP', 'can' => 'haccp.edit',     'display_name' => 'Modifier les contrôles HACCP'],
            ['category' => 'haccp', 'category_label' => 'HACCP', 'can' => 'haccp.delete',   'display_name' => 'Supprimer les contrôles HACCP'],
            ['category' => 'haccp', 'category_label' => 'HACCP', 'can' => 'haccp.validate', 'display_name' => 'Valider les contrôles HACCP'],

            // Rapports
            ['category' => 'reports', 'category_label' => 'Rapports', 'can' => 'reports.view',   'display_name' => 'Voir les rapports'],
            ['category' => 'reports', 'category_label' => 'Rapports', 'can' => 'reports.export', 'display_name' => 'Exporter les rapports'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['can' => $permission['can']],
                $permission
            );
        }
    }
}
