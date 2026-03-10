<?php

declare(strict_types=1);

namespace Domain\Product\Database\Seeders;

use Domain\Business\Models\Business;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seeds 10 products for every business that already exists in the database.
     * Does not create businesses — depends on BusinessSeeder having run first.
     * New businesses added later will receive their products on the next seed run.
     *
     * @return void
     */
    public function run(): void
    {
        /** @var Collection<int, Business> $businesses */
        $businesses = Business::all();

        foreach ($businesses as $business) {
            Product::factory()
                ->count(10)
                ->withBusiness($business)
                ->create();
        }
    }
}
