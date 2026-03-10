<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Domain\Business\Enums\PermissionEnum;
use Domain\Business\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Seeds every permission declared in PermissionEnum.
     * Identified by their canonical `can` value for idempotent re-seeding.
     * Adding a new permission to PermissionEnum is sufficient — add its display data here too.
     *
     * @return void
     */
    public function run(): void
    {
        $permissions = [
            // Members
            [
                'category'       => 'members',
                'category_label' => 'Membres',
                'can'            => PermissionEnum::MEMBERS_VIEW_ANY->value,
                'display_name'   => 'Voir tous les membres',
            ],
            [
                'category'       => 'members',
                'category_label' => 'Membres',
                'can'            => PermissionEnum::MEMBERS_VIEW->value,
                'display_name'   => 'Voir un membre',
            ],
            [
                'category'       => 'members',
                'category_label' => 'Membres',
                'can'            => PermissionEnum::MEMBERS_INVITE->value,
                'display_name'   => 'Inviter un membre',
            ],
            [
                'category'       => 'members',
                'category_label' => 'Membres',
                'can'            => PermissionEnum::MEMBERS_REMOVE->value,
                'display_name'   => 'Retirer un membre',
            ],
            [
                'category'       => 'members',
                'category_label' => 'Membres',
                'can'            => PermissionEnum::MEMBERS_EDIT->value,
                'display_name'   => 'Modifier un membre',
            ],

            // Roles
            [
                'category'       => 'roles',
                'category_label' => 'Rôles',
                'can'            => PermissionEnum::ROLES_VIEW_ANY->value,
                'display_name'   => 'Voir tous les rôles',
            ],
            [
                'category'       => 'roles',
                'category_label' => 'Rôles',
                'can'            => PermissionEnum::ROLES_VIEW->value,
                'display_name'   => 'Voir un rôle',
            ],
            [
                'category'       => 'roles',
                'category_label' => 'Rôles',
                'can'            => PermissionEnum::ROLES_CREATE->value,
                'display_name'   => 'Créer un rôle',
            ],
            [
                'category'       => 'roles',
                'category_label' => 'Rôles',
                'can'            => PermissionEnum::ROLES_EDIT->value,
                'display_name'   => 'Modifier un rôle',
            ],
            [
                'category'       => 'roles',
                'category_label' => 'Rôles',
                'can'            => PermissionEnum::ROLES_DELETE->value,
                'display_name'   => 'Supprimer un rôle',
            ],

            // Products
            [
                'category'       => 'product',
                'category_label' => 'Produits',
                'can'            => PermissionEnum::PRODUCT_VIEW_ANY->value,
                'display_name'   => 'Voir tous les produits',
            ],
            [
                'category'       => 'product',
                'category_label' => 'Produits',
                'can'            => PermissionEnum::PRODUCT_VIEW->value,
                'display_name'   => 'Voir un produit',
            ],
            [
                'category'       => 'product',
                'category_label' => 'Produits',
                'can'            => PermissionEnum::PRODUCT_CREATE->value,
                'display_name'   => 'Créer un produit',
            ],
            [
                'category'       => 'product',
                'category_label' => 'Produits',
                'can'            => PermissionEnum::PRODUCT_EDIT->value,
                'display_name'   => 'Modifier un produit',
            ],
            [
                'category'       => 'product',
                'category_label' => 'Produits',
                'can'            => PermissionEnum::PRODUCT_DELETE->value,
                'display_name'   => 'Supprimer un produit',
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::query()->updateOrCreate(
                ['can' => $permissionData['can']],
                $permissionData
            );
        }
    }
}
