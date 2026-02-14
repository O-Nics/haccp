<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organisation;
use Domain\Auth\Models\User;
use Illuminate\Database\Seeder;

class OrganisationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@haccp.local')->first();
        $jean = User::query()->where('email', 'jean@haccp.local')->first();

        $organisations = [
            [
                'owner_id' => $admin?->id,
                'name' => 'HACCP Solutions',
                'siret' => '12345678901234',
                'address' => '15 rue de la Gastronomie',
                'postal_code' => '75001',
                'city' => 'Paris',
                'country' => 'FR',
                'email' => 'contact@haccp-solutions.fr',
            ],
            [
                'owner_id' => $jean?->id,
                'name' => 'Les Délices de Jean',
                'siret' => '98765432109876',
                'address' => '42 avenue des Saveurs',
                'postal_code' => '69001',
                'city' => 'Lyon',
                'country' => 'FR',
                'email' => 'contact@delices-jean.fr',
            ],
        ];

        foreach ($organisations as $org) {
            if ($org['owner_id']) {
                Organisation::query()->updateOrCreate(
                    ['siret' => $org['siret']],
                    $org
                );
            }
        }
    }
}
