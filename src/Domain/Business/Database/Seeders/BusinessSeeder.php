<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Domain\Business\Models\Business;
use Domain\Business\Models\BusinessType;
use Domain\Business\Models\Organisation;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Seeds the 3 fixed businesses identified by their email for idempotent re-seeding.
     * Depends on OrganisationSeeder and BusinessTypeSeeder having run first.
     *
     * HACCP Solutions (alice)  → Le Gourmet Parisien (restaurant, code 5610)
     *                          → La Mie Dorée        (boulangerie, code 1071)
     * Les Délices de Diana     → Traiteur des Délices (traiteur, code 5621)
     *
     * @return void
     */
    public function run(): void
    {
        $orgHaccp   = Organisation::query()->where('siret', '12345678901234')->firstOrFail();
        $orgDelices = Organisation::query()->where('siret', '98765432109876')->firstOrFail();

        $typeRestaurant  = BusinessType::query()->where('code', 5610)->firstOrFail();
        $typeBoulangerie = BusinessType::query()->where('code', 1071)->firstOrFail();
        $typeTraiteur    = BusinessType::query()->where('code', 5621)->firstOrFail();

        $businesses = [
            [
                'organisation_id'  => $orgHaccp->id,
                'business_type_id' => $typeRestaurant->id,
                'name'             => 'Le Gourmet Parisien',
                'address'          => '15 rue de la Gastronomie',
                'postal_code'      => '75001',
                'city'             => 'Paris',
                'country'          => 'FR',
                'timezone'         => 'Europe/Paris',
                'email'            => 'gourmet@haccp-solutions.fr',
            ],
            [
                'organisation_id'  => $orgHaccp->id,
                'business_type_id' => $typeBoulangerie->id,
                'name'             => 'La Mie Dorée',
                'address'          => '8 place du Marché',
                'postal_code'      => '75002',
                'city'             => 'Paris',
                'country'          => 'FR',
                'timezone'         => 'Europe/Paris',
                'email'            => 'miedoree@haccp-solutions.fr',
            ],
            [
                'organisation_id'  => $orgDelices->id,
                'business_type_id' => $typeTraiteur->id,
                'name'             => 'Traiteur des Délices',
                'address'          => '42 avenue des Saveurs',
                'postal_code'      => '69001',
                'city'             => 'Lyon',
                'country'          => 'FR',
                'timezone'         => 'Europe/Paris',
                'email'            => 'traiteur@delices-diana.fr',
            ],
        ];

        foreach ($businesses as $businessData) {
            Business::query()->updateOrCreate(
                ['email' => $businessData['email']],
                $businessData
            );
        }
    }
}
