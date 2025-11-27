<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class BusinessRoleFactory extends Factory
{
    protected $model = BusinessRole::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'display_name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'color' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function withBusiness(?Business $restaurant): Factory
    {
        if ($restaurant === null) {
            $restaurant = Business::factory()->createQuietly();
        }

        return $this->for($restaurant, 'business');
    }
}
