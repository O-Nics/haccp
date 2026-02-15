<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Domain\Business\Models\Business;
use Domain\Business\Models\BusinessType;
use Domain\Business\Models\Organisation;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        $orgHaccp = Organisation::query()->where('siret', '12345678901234')->first();
        $orgJean = Organisation::query()->where('siret', '98765432109876')->first();

        $restaurant = BusinessType::query()->where('code', 5610)->first();
        $boulangerie = BusinessType::query()->where('code', 1071)->first();
        $traiteur = BusinessType::query()->where('code', 5621)->first();

        $businesses = [
            [
                'organisation_id' => $orgHaccp?->id,
                'business_type_id' => $restaurant?->id,
                'name' => 'Le Gourmet Parisien',
                'address' => '15 rue de la Gastronomie',
                'postal_code' => '75001',
                'city' => 'Paris',
                'country' => 'FR',
                'email' => 'gourmet@haccp-solutions.fr',
            ],
            [
                'organisation_id' => $orgHaccp?->id,
                'business_type_id' => $boulangerie?->id,
                'name' => 'La Mie Dorée',
                'address' => '8 place du Marché',
                'postal_code' => '75002',
                'city' => 'Paris',
                'country' => 'FR',
                'email' => 'miedoree@haccp-solutions.fr',
            ],
            [
                'organisation_id' => $orgJean?->id,
                'business_type_id' => $traiteur?->id,
                'name' => 'Traiteur Jean',
                'address' => '42 avenue des Saveurs',
                'postal_code' => '69001',
                'city' => 'Lyon',
                'country' => 'FR',
                'email' => 'traiteur@delices-jean.fr',
            ],
        ];

        foreach ($businesses as $business) {
            if ($business['organisation_id'] && $business['business_type_id']) {
                Business::query()->updateOrCreate(
                    ['email' => $business['email']],
                    array_merge($business, ['timezone' => 'Europe/Paris'])
                );
            }
        }
    }
}
