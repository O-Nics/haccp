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
        $admin = User::where('email', 'admin@haccp.test')->first();
        $jean = User::where('email', 'jean.dupont@haccp.test')->first();
        $marie = User::where('email', 'marie.martin@gmail.com')->first();
        $pierre = User::where('email', 'pierre.durand@haccp.test')->first();
        $sophie = User::where('email', 'sophie.leroy@haccp.test')->first();

        $ownerRole = Role::where('name', 'owner')->first();
        $managerRole = Role::where('name', 'manager')->first();

        $gourmetParisien = Business::where('name', 'Le Gourmet Parisien')->first();
        $brasserieMarche = Business::where('name', 'Brasserie du Marché')->first();
        $quickBurger = Business::where('name', 'Quick Burger')->first();

        if (! $admin || ! $jean || ! $marie || ! $ownerRole || ! $gourmetParisien) {
            return;
        }

        // Le Gourmet Parisien - Business 1
        $gerantRole = BusinessRole::where('business_id', $gourmetParisien->id)
            ->where('name', 'gerant')->first();
        $chefRole = BusinessRole::where('business_id', $gourmetParisien->id)
            ->where('name', 'chef_cuisine')->first();
        $cuisinierRole = BusinessRole::where('business_id', $gourmetParisien->id)
            ->where('name', 'cuisinier')->first();

        // Jean = Owner (system role) of Le Gourmet Parisien
        if ($gerantRole && $jean) {
            $businessUser = new BusinessUser;
            $businessUser->business_id = $gourmetParisien->id;
            $businessUser->user_id = $jean->id;
            $businessUser->role_id = $ownerRole->id;
            $businessUser->business_role_id = null;
            $businessUser->status = 'active';
            $businessUser->joined_at = now();
            $businessUser->save();
        }

        // Marie = Chef de Cuisine (business role)
        if ($chefRole && $marie) {
            $businessUser = new BusinessUser;
            $businessUser->business_id = $gourmetParisien->id;
            $businessUser->user_id = $marie->id;
            $businessUser->role_id = null;
            $businessUser->business_role_id = $chefRole->id;
            $businessUser->invited_by = $jean->id;
            $businessUser->status = 'active';
            $businessUser->invited_at = now()->subDays(10);
            $businessUser->joined_at = now()->subDays(9);
            $businessUser->save();
        }

        // Pierre = Cuisinier (business role)
        if ($cuisinierRole && $pierre) {
            $businessUser = new BusinessUser;
            $businessUser->business_id = $gourmetParisien->id;
            $businessUser->user_id = $pierre->id;
            $businessUser->role_id = null;
            $businessUser->business_role_id = $cuisinierRole->id;
            $businessUser->invited_by = $jean->id;
            $businessUser->status = 'active';
            $businessUser->invited_at = now()->subDays(5);
            $businessUser->joined_at = now()->subDays(4);
            $businessUser->save();
        }

        // Brasserie du Marché - Business 2
        if ($brasserieMarche && $sophie) {
            $gerantRole2 = BusinessRole::where('business_id', $brasserieMarche->id)
                ->where('name', 'gerant')->first();
            $serveurRole = BusinessRole::where('business_id', $brasserieMarche->id)
                ->where('name', 'serveur')->first();

            // Sophie = Manager (system role)
            if ($gerantRole2) {
                $businessUser = new BusinessUser;
                $businessUser->business_id = $brasserieMarche->id;
                $businessUser->user_id = $sophie->id;
                $businessUser->role_id = $managerRole->id;
                $businessUser->business_role_id = null;
                $businessUser->status = 'active';
                $businessUser->joined_at = now()->subDays(20);
                $businessUser->save();
            }

            // Pierre also works here as Serveur
            if ($serveurRole && $pierre) {
                $businessUser = new BusinessUser;
                $businessUser->business_id = $brasserieMarche->id;
                $businessUser->user_id = $pierre->id;
                $businessUser->role_id = null;
                $businessUser->business_role_id = $serveurRole->id;
                $businessUser->invited_by = $sophie->id;
                $businessUser->status = 'active';
                $businessUser->invited_at = now()->subDays(3);
                $businessUser->joined_at = now()->subDays(2);
                $businessUser->save();
            }
        }

        // Quick Burger - Business 3
        if ($quickBurger && $admin) {
            $gerantRole3 = BusinessRole::where('business_id', $quickBurger->id)
                ->where('name', 'gerant')->first();

            // Admin = Owner of Quick Burger
            if ($gerantRole3) {
                $businessUser = new BusinessUser;
                $businessUser->business_id = $quickBurger->id;
                $businessUser->user_id = $admin->id;
                $businessUser->role_id = $ownerRole->id;
                $businessUser->business_role_id = null;
                $businessUser->status = 'active';
                $businessUser->joined_at = now()->subDays(30);
                $businessUser->save();
            }
        }

        // Add random users to businesses with random roles
        $randomUsers = User::whereNotIn('email', [
            'admin@haccp.test',
            'jean.dupont@haccp.test',
            'marie.martin@haccp.test',
            'pierre.durand@haccp.test',
            'sophie.leroy@haccp.test',
        ])->limit(10)->get();

        $allBusinesses = Business::all();

        foreach ($randomUsers as $user) {
            $business = $allBusinesses->random();
            $randomBusinessRole = BusinessRole::where('business_id', $business->id)->inRandomOrder()->first();

            if ($randomBusinessRole) {
                $businessUser = new BusinessUser;
                $businessUser->business_id = $business->id;
                $businessUser->user_id = $user->id;
                $businessUser->role_id = null;
                $businessUser->business_role_id = $randomBusinessRole->id;
                $businessUser->status = rand(0, 1) ? 'active' : 'invited';
                $businessUser->invited_at = now()->subDays(rand(1, 60));
                if ($businessUser->status === 'active') {
                    $businessUser->joined_at = now()->subDays(rand(1, 50));
                }
                $businessUser->save();
            }
        }
    }
}
