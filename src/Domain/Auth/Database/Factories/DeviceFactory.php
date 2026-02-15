<?php

declare(strict_types=1);

namespace Domain\Auth\Database\Factories;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Device>
 */
class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'device_id' => Str::uuid()->toString(),
            'platform' => fake()->randomElement(['ios', 'android', 'web', 'desktop']),
            'ip_address' => fake()->ipv4(),
            'model' => fake()->optional()->randomElement(['iPhone 15', 'Samsung Galaxy S24', 'MacBook Pro', 'Windows PC']),
            'remember_me' => fake()->boolean(),
        ];
    }

    public function withUser(User|array|null $user = null): static
    {
        $instance = $user instanceof User
            ? $user
            : User::factory()->create(is_array($user) ? $user : []);

        return $this->for($instance, 'user');
    }

    public function rememberMe(): static
    {
        return $this->state(fn (array $attributes) => [
            'remember_me' => true,
        ]);
    }
}
