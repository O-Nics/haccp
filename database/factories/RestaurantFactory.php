<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\RestaurantType;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantFactory extends Factory
{
    protected $model = Restaurant::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'siret' => $this->faker->unique()->numerify('##############'),
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => $this->faker->countryCode(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }

    public function withRestaurantType(?RestaurantType $type = null): Factory
    {
        if ($type === null) {
            $type = RestaurantType::factory()->createQuietly();
        }

        return $this->for($type, 'restaurantType');
    }
}
