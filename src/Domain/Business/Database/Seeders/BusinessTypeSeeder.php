<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Domain\Business\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Restaurant', 'code' => 5610],
            ['name' => 'Boulangerie', 'code' => 1071],
            ['name' => 'Pâtisserie', 'code' => 1072],
            ['name' => 'Boucherie', 'code' => 4722],
            ['name' => 'Traiteur', 'code' => 5621],
            ['name' => 'Café-Bar', 'code' => 5630],
            ['name' => 'Hôtel-Restaurant', 'code' => 5510],
            ['name' => 'Cantine', 'code' => 5629],
            ['name' => 'Food Truck', 'code' => 5611],
        ];

        foreach ($types as $type) {
            BusinessType::query()->updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
