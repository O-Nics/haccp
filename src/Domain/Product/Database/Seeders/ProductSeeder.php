<?php

namespace Domain\Product\Database\Seeders;

use Domain\Business\Models\Business;
use Domain\Product\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businesses = Business::factory()->count(5)->create();

        foreach ($businesses as $business) {
            Product::factory()->count(10)->withBusiness($business)->create();
        }
    }
}
