<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific test users with known credentials
        $testUsers = [
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'email' => 'marie.martin@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Durand',
                'email' => 'pierre.durand@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Leroy',
                'email' => 'sophie.leroy@gmail.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ];

        foreach ($testUsers as $userData) {
            $user = new User;
            $user->first_name = $userData['first_name'];
            $user->last_name = $userData['last_name'];
            $user->email = $userData['email'];
            $user->password = $userData['password'];
            $user->email_verified_at = $userData['email_verified_at'];
            $user->save();


        }

        // Create additional random users for testing
        User::factory()->count(15)->create();
    }
}
