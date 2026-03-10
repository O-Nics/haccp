<?php

declare(strict_types=1);

namespace Domain\Auth\Database\Seeders;

use Domain\Auth\Models\Device;
use Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Email addresses of the fixed test users created by UserSeeder.
     * Kept in sync with UserSeeder::$fixedUsers.
     */
    private array $fixedEmails = [
        'alice.owner@haccp.local',
        'bob.multi@haccp.local',
        'charlie.free@haccp.local',
        'diana.owner@haccp.local',
    ];

    /**
     * Seeds one predictable web device per fixed user then one device per extra user.
     * Fixed devices always use the web platform so tests have a consistent baseline.
     *
     * @return void
     */
    public function run(): void
    {
        $fixedUsers = User::query()
            ->whereIn('email', $this->fixedEmails)
            ->get();

        foreach ($fixedUsers as $fixedUser) {
            Device::factory()
                ->withUser($fixedUser)
                ->create([
                    'platform'    => 'web',
                    'remember_me' => false,
                ]);
        }

        /** @var Collection<int, User> $extraUsers */
        $extraUsers = User::query()
            ->whereNotIn('email', $this->fixedEmails)
            ->get();

        foreach ($extraUsers as $extraUser) {
            Device::factory()
                ->withUser($extraUser)
                ->create();
        }
    }
}
