<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Business>
 */
class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'timezone' => fake()->timezone(),
            'country' => fake()->countryCode(),
            'phone' => fake()->optional()->phoneNumber(),
            'email' => fake()->unique()->companyEmail(),
        ];
    }

    public function withBusinessType(BusinessType|array|null $businessType = null): static
    {
        $instance = $businessType instanceof BusinessType
            ? $businessType
            : BusinessType::factory()->create(is_array($businessType) ? $businessType : []);

        return $this->for($instance, 'businessType');
    }

    public function withOrganisation(Organisation|array|null $organisation = null): static
    {
        $instance = $organisation instanceof Organisation
            ? $organisation
            : Organisation::factory()->withOwner()->create(is_array($organisation) ? $organisation : []);

        return $this->for($instance, 'organisation');
    }
}
