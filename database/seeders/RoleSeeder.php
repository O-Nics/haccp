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
        ];

        foreach ($roles as $roleData) {
            if (! Role::where('name', $roleData['name'])->exists()) {
                Role::create($roleData);
            }
        }
    }
}
