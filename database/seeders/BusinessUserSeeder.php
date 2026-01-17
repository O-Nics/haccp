<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessRole;
use App\Models\BusinessUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class BusinessUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Link users to businesss with specific roles
     */
    public function run(): void
    {
        $users = User::all();
        $businesses = Business::all();
        $roles = Role::all();

        foreach ($users as $user) {
            // Assign each user to a random business with a random role
            $business = $businesses->random();
            $role = $roles->random();

            BusinessUser::create([
                'user_id' => $user->id,
                'business_id' => $business->id,
                'role_id' => Role::where('id', $role->id)
                    ->inRandomOrder()
                    ->first()
                    ->id,
            ]);
        }
    }
}
