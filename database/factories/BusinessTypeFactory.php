<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BusinessType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessType>
 */
class BusinessTypeFactory extends Factory
{
    protected $model = BusinessType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Restaurant',
                'Boulangerie',
                'Pâtisserie',
                'Boucherie',
                'Traiteur',
                'Café',
                'Bar',
                'Hôtel',
                'Cantine',
                'Food Truck',
            ]),
            'code' => fake()->unique()->numberBetween(1000, 9999),
        ];
    }
}
