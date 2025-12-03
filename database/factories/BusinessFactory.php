<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\BusinessType;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessFactory extends Factory
{
    protected $model = Business::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => $this->faker->countryCode(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }

    public function withBusinessType(?BusinessType $type = null): Factory
    {
        if ($type === null) {
            $type = BusinessType::factory()->createQuietly();
        }

        return $this->for($type, 'businessType');
    }

    public function withOrganisation(?Organisation $organisation = null, ?User $user = null): Factory
    {
        if ($organisation === null) {
            $organisation = Organisation::factory()->withOwner($user)->createQuietly();
        }

        return $this->for($organisation, 'organisation');
    }
}
