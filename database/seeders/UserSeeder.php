<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'email' => 'admin@haccp.local',
            ],
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean@haccp.local',
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'email' => 'marie@haccp.local',
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Bernard',
                'email' => 'pierre@haccp.local',
            ],
            [
                'first_name' => 'Sophie',
                'last_name' => 'Petit',
                'email' => 'sophie@haccp.local',
            ],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                array_merge($user, [
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'local' => 'fr_FR',
                    'timezone' => 'Europe/Paris',
                ])
            );
        }
    }
}
