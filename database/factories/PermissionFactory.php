<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $displayName = fake()->unique()->words(2, true);
        $name = Str::snake($displayName);

        return [
            'name' => $name,
            'display_name' => ucwords($displayName),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
