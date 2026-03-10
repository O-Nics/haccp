<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Domain\Auth\Models\User;
use Domain\Business\Models\Organisation;
use Illuminate\Database\Seeder;

class OrganisationSeeder extends Seeder
{
    /**
     * Seeds the 2 fixed organisations via the owner's ORM relationship.
     * Identified by SIRET for idempotent re-seeding.
     *
     * HACCP Solutions     → alice.owner@haccp.local (2 businesses under this org)
     * Les Délices de Diana → diana.owner@haccp.local (1 business under this org)
     *
     * @return void
     */
    public function run(): void
    {
        $alice = User::query()->where('email', 'alice.owner@haccp.local')->firstOrFail();
        $diana = User::query()->where('email', 'diana.owner@haccp.local')->firstOrFail();

        $alice->organizations()->updateOrCreate(
            ['siret' => '12345678901234'],
            [
                'name'        => 'HACCP Solutions',
                'address'     => '15 rue de la Gastronomie',
                'postal_code' => '75001',
                'city'        => 'Paris',
                'country'     => 'FR',
                'email'       => 'contact@haccp-solutions.fr',
            ]
        );

        $diana->organizations()->updateOrCreate(
            ['siret' => '98765432109876'],
            [
                'name'        => 'Les Délices de Diana',
                'address'     => '42 avenue des Saveurs',
                'postal_code' => '69001',
                'city'        => 'Lyon',
                'country'     => 'FR',
                'email'       => 'contact@delices-diana.fr',
            ]
        );
    }
}
