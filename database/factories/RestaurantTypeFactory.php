<?php

namespace Database\Factories;

use App\Models\RestaurantType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantTypeFactory extends Factory
{
    protected $model = RestaurantType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'code' => $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }
}
