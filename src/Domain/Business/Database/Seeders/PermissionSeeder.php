<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Members
            ['category' => 'members', 'category_label' => 'Membres', 'can' => PermissionEnum::MEMBERS_VIEW->value,   'display_name' => 'Voir les membres'],
            ['category' => 'members', 'category_label' => 'Membres', 'can' => PermissionEnum::MEMBERS_INVITE->value, 'display_name' => 'Inviter un membre'],
            ['category' => 'members', 'category_label' => 'Membres', 'can' => PermissionEnum::MEMBERS_REMOVE->value, 'display_name' => 'Retirer un membre'],
            ['category' => 'members', 'category_label' => 'Membres', 'can' => PermissionEnum::MEMBERS_EDIT->value,   'display_name' => 'Modifier un membre'],

            // Roles
            ['category' => 'roles', 'category_label' => 'Rôles', 'can' => PermissionEnum::ROLES_VIEW->value,   'display_name' => 'Voir les rôles'],
            ['category' => 'roles', 'category_label' => 'Rôles', 'can' => PermissionEnum::ROLES_CREATE->value, 'display_name' => 'Créer un rôle'],
            ['category' => 'roles', 'category_label' => 'Rôles', 'can' => PermissionEnum::ROLES_EDIT->value,   'display_name' => 'Modifier un rôle'],
            ['category' => 'roles', 'category_label' => 'Rôles', 'can' => PermissionEnum::ROLES_DELETE->value, 'display_name' => 'Supprimer un rôle'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['can' => $permission['can']],
                $permission
            );
        }
    }
}
