<?php

namespace Database\Factories;

use App\Features\Session\Enums\DeviceType;
use App\Features\Session\Enums\Platform;
use App\Models\Device;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'device_id' => Str::uuid()->toString(),
            'device_name' => $this->faker->name(),
            'device_type' => $this->faker->randomElement(DeviceType::availableTypes()),
            'platform' => $this->faker->randomElement(Platform::availablePlatforms()),
            'browser' => $this->faker->word(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->word(),
            'refresh_token_hash' => Str::random(10),
            'token_expires_at' => Carbon::now(),
            'last_used_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function withUser(User|array|null $user = null): static
    {
        $instance = $user instanceof User
            ? $user
            : User::factory()->create(is_array($user) ? $user : []);

        return $this->for($instance, 'user');
    }
}
