<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Organisation;
use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organisation>
 */
class OrganisationFactory extends Factory
{
    protected $model = Organisation::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'siret' => fake()->unique()->numerify('##############'),
            'address' => fake()->streetAddress(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'country' => fake()->countryCode(),
            'phone' => fake()->optional()->phoneNumber(),
            'email' => fake()->optional()->companyEmail(),
        ];
    }

    public function withOwner(User|array|null $user = null): static
    {
        $instance = $user instanceof User
            ? $user
            : User::factory()->create(is_array($user) ? $user : []);

        return $this->for($instance, 'owner');
    }
}
