<?php

declare(strict_types=1);

namespace Domain\Business\Database\Factories;

use Domain\Auth\Models\User;
use Domain\Business\Models\Business;
use Domain\Business\Models\BusinessUser;
use Domain\Business\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusinessUser>
 */
class BusinessUserFactory extends Factory
{
    protected $model = BusinessUser::class;

    public function definition(): array
    {
        return [
            'invited_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'joined_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'status' => fake()->randomElement(['invited', 'active', 'inactive']),
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

    public function withUser(User|array|null $user = null): static
    {
        $instance = $user instanceof User
            ? $user
            : User::factory()->create(is_array($user) ? $user : []);

        return $this->for($instance, 'user');
    }

    public function withInvitedBy(User|array|null $user = null): static
    {
        $instance = $user instanceof User
            ? $user
            : User::factory()->create(is_array($user) ? $user : []);

        return $this->for($instance, 'invitedBy');
    }

    public function withRole(Role|array|null $role = null): static
    {
        $instance = $role instanceof Role
            ? $role
            : Role::factory()->withBusiness()->create(is_array($role) ? $role : []);

        return $this->for($instance, 'role');
    }

    public function invited(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'invited',
            'invited_at' => now(),
            'joined_at' => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'invited_at' => now()->subDays(7),
            'joined_at' => now(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
