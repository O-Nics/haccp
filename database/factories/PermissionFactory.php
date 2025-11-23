<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $displayName = $this->faker->unique()->name();
        $name = str_replace(' ', '_', strtolower($displayName));

        return [
            'name' => $name,
            'display_name' => $$displayName,
            'description' => $this->faker->text(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
