<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'pin_code' => fake()->optional()->numerify('######'),
            'notify_email' => fake()->boolean(),
            'notify_sms' => fake()->boolean(),
            'local' => fake()->randomElement(['fr_FR', 'en_US', 'en_GB']),
            'timezone' => fake()->timezone(),
            'last_login_at' => fake()->optional()->dateTime(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }
}
