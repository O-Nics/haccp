<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\RestaurantRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RestaurantRoleFactory extends Factory
{
    protected $model = RestaurantRole::class;

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

    public function withRestaurant(?Restaurant $restaurant): Factory
    {
        if ($restaurant === null) {
            $restaurant = Restaurant::factory()->createQuietly();
        }

        return $this->for($restaurant, 'restaurant');
    }
}
