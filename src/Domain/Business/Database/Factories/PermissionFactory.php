<?php

declare(strict_types=1);

namespace Domain\Business\Database\Factories;

use Domain\Business\Models\Permission;
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
        $category = $this->faker->word();
        $action = $this->faker->randomElement(['view', 'create', 'update', 'delete']);
        $can = "{$category}.{$action}";

        return [
            'can' => $can,
            'category' => $category,
            'category_label' => Str::title($category),
            'display_name' => Str::title(str_replace('.', ' ', $can)),
        ];
    }
}
