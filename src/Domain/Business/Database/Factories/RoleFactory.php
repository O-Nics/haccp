<?php

declare(strict_types=1);

namespace Domain\Business\Database\Factories;

use Domain\Business\Models\Business;
use Domain\Business\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        return [
            'is_system' => false,
            'name' => $name,
            'display_name' => ucfirst($name),
            'description' => fake()->optional()->sentence(),
            'color' => fake()->hexColor(),
        ];
    }

    public function withBusiness(Business|array|null $business = null): static
    {
        $instance = $business instanceof Business
            ? $business
            : Business::factory()
                ->withBusinessType()
                ->withOrganisation()
                ->create(is_array($business) ? $business : []);

        return $this->for($instance, 'business');
    }

    public function system(): static
    {
        return $this->state(function (array $attributes): array {
            return ['is_system' => true];
        });
    }
}
