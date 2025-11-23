<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantRole;
use App\Models\RestaurantUser;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RestaurantUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Link users to restaurants with specific roles
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@haccp.test')->first();
        $jean = User::where('email', 'jean.dupont@haccp.test')->first();
        $marie = User::where('email', 'marie.martin@haccp.test')->first();
        $pierre = User::where('email', 'pierre.durand@haccp.test')->first();
        $sophie = User::where('email', 'sophie.leroy@haccp.test')->first();

        $ownerRole = Role::where('name', 'owner')->first();
        $managerRole = Role::where('name', 'manager')->first();

        $gourmetParisien = Restaurant::where('name', 'Le Gourmet Parisien')->first();
        $brasserieMarche = Restaurant::where('name', 'Brasserie du Marché')->first();
        $quickBurger = Restaurant::where('name', 'Quick Burger')->first();

        if (! $admin || ! $jean || ! $marie || ! $ownerRole || ! $gourmetParisien) {
            return;
        }

        // Le Gourmet Parisien - Restaurant 1
        $gerantRole = RestaurantRole::where('restaurant_id', $gourmetParisien->id)
            ->where('name', 'gerant')->first();
        $chefRole = RestaurantRole::where('restaurant_id', $gourmetParisien->id)
            ->where('name', 'chef_cuisine')->first();
        $cuisinierRole = RestaurantRole::where('restaurant_id', $gourmetParisien->id)
            ->where('name', 'cuisinier')->first();

        // Jean = Owner (system role) of Le Gourmet Parisien
        if ($gerantRole && $jean) {
            $ru = new RestaurantUser;
            $ru->restaurant_id = $gourmetParisien->id;
            $ru->user_id = $jean->id;
            $ru->role_id = $ownerRole->id;
            $ru->restaurant_role_id = null;
            $ru->status = 'active';
            $ru->joined_at = now();
            $ru->save();
        }

        // Marie = Chef de Cuisine (restaurant role)
        if ($chefRole && $marie) {
            $ru = new RestaurantUser;
            $ru->restaurant_id = $gourmetParisien->id;
            $ru->user_id = $marie->id;
            $ru->role_id = null;
            $ru->restaurant_role_id = $chefRole->id;
            $ru->invited_by = $jean->id;
            $ru->status = 'active';
            $ru->invited_at = now()->subDays(10);
            $ru->joined_at = now()->subDays(9);
            $ru->save();
        }

        // Pierre = Cuisinier (restaurant role)
        if ($cuisinierRole && $pierre) {
            $ru = new RestaurantUser;
            $ru->restaurant_id = $gourmetParisien->id;
            $ru->user_id = $pierre->id;
            $ru->role_id = null;
            $ru->restaurant_role_id = $cuisinierRole->id;
            $ru->invited_by = $jean->id;
            $ru->status = 'active';
            $ru->invited_at = now()->subDays(5);
            $ru->joined_at = now()->subDays(4);
            $ru->save();
        }

        // Brasserie du Marché - Restaurant 2
        if ($brasserieMarche && $sophie) {
            $gerantRole2 = RestaurantRole::where('restaurant_id', $brasserieMarche->id)
                ->where('name', 'gerant')->first();
            $serveurRole = RestaurantRole::where('restaurant_id', $brasserieMarche->id)
                ->where('name', 'serveur')->first();

            // Sophie = Manager (system role)
            if ($gerantRole2) {
                $ru = new RestaurantUser;
                $ru->restaurant_id = $brasserieMarche->id;
                $ru->user_id = $sophie->id;
                $ru->role_id = $managerRole->id;
                $ru->restaurant_role_id = null;
                $ru->status = 'active';
                $ru->joined_at = now()->subDays(20);
                $ru->save();
            }

            // Pierre also works here as Serveur
            if ($serveurRole && $pierre) {
                $ru = new RestaurantUser;
                $ru->restaurant_id = $brasserieMarche->id;
                $ru->user_id = $pierre->id;
                $ru->role_id = null;
                $ru->restaurant_role_id = $serveurRole->id;
                $ru->invited_by = $sophie->id;
                $ru->status = 'active';
                $ru->invited_at = now()->subDays(3);
                $ru->joined_at = now()->subDays(2);
                $ru->save();
            }
        }

        // Quick Burger - Restaurant 3
        if ($quickBurger && $admin) {
            $gerantRole3 = RestaurantRole::where('restaurant_id', $quickBurger->id)
                ->where('name', 'gerant')->first();

            // Admin = Owner of Quick Burger
            if ($gerantRole3) {
                $ru = new RestaurantUser;
                $ru->restaurant_id = $quickBurger->id;
                $ru->user_id = $admin->id;
                $ru->role_id = $ownerRole->id;
                $ru->restaurant_role_id = null;
                $ru->status = 'active';
                $ru->joined_at = now()->subDays(30);
                $ru->save();
            }
        }

        // Add random users to restaurants with random roles
        $randomUsers = User::whereNotIn('email', [
            'admin@haccp.test',
            'jean.dupont@haccp.test',
            'marie.martin@haccp.test',
            'pierre.durand@haccp.test',
            'sophie.leroy@haccp.test',
        ])->limit(10)->get();

        $allRestaurants = Restaurant::all();

        foreach ($randomUsers as $user) {
            $restaurant = $allRestaurants->random();
            $randomRestaurantRole = RestaurantRole::where('restaurant_id', $restaurant->id)->inRandomOrder()->first();

            if ($randomRestaurantRole) {
                $ru = new RestaurantUser;
                $ru->restaurant_id = $restaurant->id;
                $ru->user_id = $user->id;
                $ru->role_id = null;
                $ru->restaurant_role_id = $randomRestaurantRole->id;
                $ru->status = rand(0, 1) ? 'active' : 'invited';
                $ru->invited_at = now()->subDays(rand(1, 60));
                if ($ru->status === 'active') {
                    $ru->joined_at = now()->subDays(rand(1, 50));
                }
                $ru->save();
            }
        }
    }
}