<?php

namespace Database\Factories;

use App\Models\Organisation;
use App\Models\OrganisationRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrganisationRoleFactory extends Factory
{
    protected $model = OrganisationRole::class;

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

    public function withOrganisation(?Organisation $organisation): Factory
    {
        if ($organisation === null) {
            $organisation = Organisation::factory()->createQuietly();
        }

        return $this->for($organisation, 'organisation');
    }
}
