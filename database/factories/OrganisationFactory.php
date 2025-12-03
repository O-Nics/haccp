<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganisationFactory extends Factory
{
    protected $model = Organisation::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'siret' => $this->faker->unique()->numerify('###########'),
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => $this->faker->countryCode(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->safeEmail(),

        ];
    }

    public function withOwner(?User $user = null): static
    {
        if ($user === null) {
            $user = User::factory()->createQuietly();
        }

        return $this->for($user, 'owner');
    }
}
