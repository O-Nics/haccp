<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Features\Notifications\Enums\NotificationChannel;
use App\Features\Notifications\Enums\NotificationSentStatus;
use App\Models\NotificationSent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationSent>
 */
class NotificationSentFactory extends Factory
{
    protected $model = NotificationSent::class;

    public function definition(): array
    {
        return [
            'recipient' => fake()->email(),
            'name' => fake()->randomElement(['WelcomeEmail', 'PasswordReset', 'NewDeviceLogin', 'InvitationSent']),
            'type' => NotificationChannel::Email,
            'status' => NotificationSentStatus::Sent,
            'subject' => fake()->sentence(),
            'content_html' => fake()->randomHtml(),
            'content_text' => fake()->paragraph(),
            'resent_at' => null,
            'error_message' => null,
        ];
    }

    public function withUser(User|array|null $user = null): static
    {
        $instance = $user instanceof User
            ? $user
            : User::factory()->create(is_array($user) ? $user : []);

        return $this->for($instance, 'user');
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationSentStatus::Sent,
            'error_message' => null,
        ]);
    }

    public function error(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationSentStatus::Error,
            'error_message' => fake()->sentence(),
        ]);
    }

    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationSentStatus::Blocked,
            'error_message' => null,
        ]);
    }

    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => NotificationChannel::Email,
            'recipient' => fake()->email(),
        ]);
    }

    public function sms(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => NotificationChannel::Sms,
            'recipient' => fake()->phoneNumber(),
        ]);
    }

    public function resent(): static
    {
        return $this->state(fn (array $attributes) => [
            'resent_at' => now(),
        ]);
    }
}
