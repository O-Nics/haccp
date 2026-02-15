<?php

declare(strict_types=1);

namespace Domain\Auth\Database\Factories;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\Session;
use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Session>
 */
class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition(): array
    {
        return [];
    }

    public function withUser(User|array|null $user = null): static
    {
        $instance = $user instanceof User
            ? $user
            : User::factory()->create(is_array($user) ? $user : []);

        return $this->for($instance, 'user');
    }

    public function withDevice(Device|array|null $device = null): static
    {
        $instance = $device instanceof Device
            ? $device
            : Device::factory()->withUser()->create(is_array($device) ? $device : []);

        return $this->for($instance, 'device');
    }
}
