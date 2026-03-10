<?php

declare(strict_types=1);

namespace Domain\Auth\Database\Seeders;

use Domain\Auth\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Fixed test users — credentials and scenarios never change.
     *
     * The local part of the email encodes the test scenario so the
     * right user can be identified at a glance during debugging:
     *
     *   alice.owner@haccp.local   → 1 seul business, rôle owner
     *   bob.multi@haccp.local     → plusieurs businesses, 2 rôles différents (owner + manager)
     *   charlie.free@haccp.local  → aucune membership, aucun business
     *   diana.owner@haccp.local   → owner du Traiteur des Délices (2ème organisation)
     *
     * Password: password (hashed via User model cast on write).
     */
    private array $fixedUsers = [
        [
            'first_name'   => 'Alice',
            'last_name'    => 'Dupont',
            'email'        => 'alice.owner@haccp.local',
            'password'     => 'password',
            'local'        => 'fr_FR',
            'notify_email' => true,
            'notify_sms'   => false,
        ],
        [
            'first_name'   => 'Bob',
            'last_name'    => 'Martin',
            'email'        => 'bob.multi@haccp.local',
            'password'     => 'password',
            'local'        => 'fr_FR',
            'notify_email' => true,
            'notify_sms'   => false,
        ],
        [
            'first_name'   => 'Charlie',
            'last_name'    => 'Petit',
            'email'        => 'charlie.free@haccp.local',
            'password'     => 'password',
            'local'        => 'fr_FR',
            'notify_email' => false,
            'notify_sms'   => false,
        ],
        [
            'first_name'   => 'Diana',
            'last_name'    => 'Leroy',
            'email'        => 'diana.owner@haccp.local',
            'password'     => 'password',
            'local'        => 'fr_FR',
            'notify_email' => true,
            'notify_sms'   => true,
        ],
    ];

    /**
     * Seeds the 4 fixed users then fills with random users for realistic data volume.
     * Fixed users are upserted so re-seeding is always idempotent.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->fixedUsers as $userData) {
            User::query()->updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, ['email_verified_at' => now()])
            );
        }

        User::factory()->count(8)->create();
    }
}
