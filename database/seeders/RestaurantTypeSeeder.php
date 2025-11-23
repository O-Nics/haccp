<?php

namespace Database\Seeders;

use App\Models\RestaurantType;
use Illuminate\Database\Seeder;

class RestaurantTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['traditional', 'brasserie', 'fast_food', 'cafe', 'bakery', 'caterer', 'food_truck', 'other'];
        foreach ($types as $type) {
            if (! RestaurantType::where('name', $type)->exists()) {
                RestaurantType::factory()->create(['name' => $type]);
            }
        }
    }
}
