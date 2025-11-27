<?php

declare(strict_types=1);

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
        $permissions = [
            // User Management
            ['name' => 'user.view_any', 'display_name' => 'Voir les utilisateurs', 'description' => 'Voir la liste des utilisateurs'],
            ['name' => 'user.view', 'display_name' => 'Voir un utilisateur', 'description' => 'Voir les détails d\'un utilisateur'],
            ['name' => 'user.create', 'display_name' => 'Créer un utilisateur', 'description' => 'Créer un nouvel utilisateur'],
            ['name' => 'user.update', 'display_name' => 'Modifier un utilisateur', 'description' => 'Modifier les informations d\'un utilisateur'],
            ['name' => 'user.delete', 'display_name' => 'Supprimer un utilisateur', 'description' => 'Supprimer un utilisateur'],

            // Business Management
            ['name' => 'restaurant.view_any', 'display_name' => 'Voir les restaurants', 'description' => 'Voir la liste des restaurants'],
            ['name' => 'restaurant.view', 'display_name' => 'Voir un restaurant', 'description' => 'Voir les détails d\'un restaurant'],
            ['name' => 'restaurant.create', 'display_name' => 'Créer un restaurant', 'description' => 'Créer un nouveau restaurant'],
            ['name' => 'restaurant.update', 'display_name' => 'Modifier un restaurant', 'description' => 'Modifier les informations d\'un restaurant'],
            ['name' => 'restaurant.delete', 'display_name' => 'Supprimer un restaurant', 'description' => 'Supprimer un restaurant'],

            // Business Role Management
            ['name' => 'restaurant_role.view_any', 'display_name' => 'Voir les rôles', 'description' => 'Voir la liste des rôles du restaurant'],
            ['name' => 'restaurant_role.view', 'display_name' => 'Voir un rôle', 'description' => 'Voir les détails d\'un rôle'],
            ['name' => 'restaurant_role.create', 'display_name' => 'Créer un rôle', 'description' => 'Créer un nouveau rôle personnalisé'],
            ['name' => 'restaurant_role.update', 'display_name' => 'Modifier un rôle', 'description' => 'Modifier un rôle existant'],
            ['name' => 'restaurant_role.delete', 'display_name' => 'Supprimer un rôle', 'description' => 'Supprimer un rôle personnalisé'],

            // Business User Management
            ['name' => 'restaurant_user.view_any', 'display_name' => 'Voir les employés', 'description' => 'Voir la liste des employés'],
            ['name' => 'restaurant_user.view', 'display_name' => 'Voir un employé', 'description' => 'Voir les détails d\'un employé'],
            ['name' => 'restaurant_user.invite', 'display_name' => 'Inviter un employé', 'description' => 'Inviter un nouvel employé'],
            ['name' => 'restaurant_user.update', 'display_name' => 'Modifier un employé', 'description' => 'Modifier le rôle d\'un employé'],
            ['name' => 'restaurant_user.delete', 'display_name' => 'Retirer un employé', 'description' => 'Retirer un employé du restaurant'],

            // HACCP - Temperature Logs
            ['name' => 'temperature.view_any', 'display_name' => 'Voir les relevés de température', 'description' => 'Voir tous les relevés de température'],
            ['name' => 'temperature.view', 'display_name' => 'Voir un relevé', 'description' => 'Voir un relevé de température'],
            ['name' => 'temperature.create', 'display_name' => 'Créer un relevé', 'description' => 'Enregistrer un relevé de température'],
            ['name' => 'temperature.update', 'display_name' => 'Modifier un relevé', 'description' => 'Modifier un relevé de température'],
            ['name' => 'temperature.delete', 'display_name' => 'Supprimer un relevé', 'description' => 'Supprimer un relevé de température'],

            // HACCP - Cleaning Logs
            ['name' => 'cleaning.view_any', 'display_name' => 'Voir les nettoyages', 'description' => 'Voir tous les logs de nettoyage'],
            ['name' => 'cleaning.view', 'display_name' => 'Voir un nettoyage', 'description' => 'Voir un log de nettoyage'],
            ['name' => 'cleaning.create', 'display_name' => 'Créer un nettoyage', 'description' => 'Enregistrer un nettoyage'],
            ['name' => 'cleaning.update', 'display_name' => 'Modifier un nettoyage', 'description' => 'Modifier un log de nettoyage'],
            ['name' => 'cleaning.delete', 'display_name' => 'Supprimer un nettoyage', 'description' => 'Supprimer un log de nettoyage'],

            // HACCP - Reception Logs
            ['name' => 'reception.view_any', 'display_name' => 'Voir les réceptions', 'description' => 'Voir tous les logs de réception'],
            ['name' => 'reception.view', 'display_name' => 'Voir une réception', 'description' => 'Voir un log de réception'],
            ['name' => 'reception.create', 'display_name' => 'Créer une réception', 'description' => 'Enregistrer une réception de marchandise'],
            ['name' => 'reception.update', 'display_name' => 'Modifier une réception', 'description' => 'Modifier un log de réception'],
            ['name' => 'reception.delete', 'display_name' => 'Supprimer une réception', 'description' => 'Supprimer un log de réception'],

            // Reports & Analytics
            ['name' => 'report.view', 'display_name' => 'Voir les rapports', 'description' => 'Consulter les rapports HACCP'],
            ['name' => 'report.export', 'display_name' => 'Exporter les rapports', 'description' => 'Exporter les rapports en PDF/Excel'],

            // Settings
            ['name' => 'settings.view', 'display_name' => 'Voir les paramètres', 'description' => 'Consulter les paramètres du restaurant'],
            ['name' => 'settings.update', 'display_name' => 'Modifier les paramètres', 'description' => 'Modifier les paramètres du restaurant'],
        ];

        foreach ($permissions as $permissionData) {
            $permission = new Permission;
            $permission->name = $permissionData['name'];
            $permission->display_name = $permissionData['display_name'];
            $permission->description = $permissionData['description'];
            $permission->save();
        }
    }
}
