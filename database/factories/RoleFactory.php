<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'display_name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'color' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function withBusiness(?Business $business): Factory
    {
        if ($business === null) {
            $business = Business::factory()->createQuietly();
        }

        return $this->for($business, 'business');
    }
}
