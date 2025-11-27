<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['traditional', 'brasserie', 'fast_food', 'cafe', 'bakery', 'caterer', 'food_truck', 'other'];
        foreach ($types as $type) {
            if (! BusinessType::where('name', $type)->exists()) {
                BusinessType::factory()->create(['name' => $type]);
            }
        }
    }
}
