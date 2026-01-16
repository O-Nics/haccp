<?php

namespace Database\Factories;

use App\Features\Notifications\Enums\NotificationChannel;
use App\Features\Notifications\Enums\NotificationSentStatus;
use App\Models\NotificationSent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NotificationSentFactory extends Factory
{
    protected $model = NotificationSent::class;

    public function definition(): array
    {
        return [
            'recipient' => $this->faker->word(),
            'name' => $this->faker->name(),
            'type' => $this->faker->word(),
            'status' => $this->faker->word(),
            'subject' => $this->faker->word(),
            'content' => $this->faker->word(),
            'resent_at' => Carbon::now(),
            'error_message' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function sent(): static
    {
        return $this->state([
            'status' => NotificationSentStatus::Sent,
            'error_message' => null,
        ]);
    }

    public function error(): static
    {
        return $this->state([
            'status' => NotificationSentStatus::Error,
            'error_message' => fake()->sentence(),
        ]);
    }

    public function email(): static
    {
        return $this->state([
            'type' => NotificationChannel::Email,
            'recipient' => fake()->email(),
        ]);
    }

    public function sms(): static
    {
        return $this->state([
            'type' => NotificationChannel::Sms,
            'recipient' => fake()->phoneNumber(),
        ]);
    }

    public function resent(): static
    {
        return $this->state([
            'resent_at' => now(),
        ]);
    }

    public function withUser(?User $user = null): static
    {
        if (! $user) {
            $user = User::factory()->create();
        }

        return $this->for($user, 'user');
    }
}
