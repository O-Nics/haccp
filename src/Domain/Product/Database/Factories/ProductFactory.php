<?php

declare(strict_types=1);

namespace Domain\Product\Database\Factories;

use Domain\Business\Models\Business;
use Domain\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
        ];
    }

    public function withBusiness(Business|array|null $business = null): static
    {
        $instance = $business instanceof Business
            ? $business
            : Business::factory()->create(is_array($business) ? $business : []);

        return $this->for($instance, 'business');
    }
}
