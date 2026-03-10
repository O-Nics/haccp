<?php

declare(strict_types=1);

namespace Domain\Business\Database\Seeders;

use Carbon\Carbon;
use Domain\Auth\Models\User;
use Domain\Business\Enums\MemberStatusEnum;
use Domain\Business\Enums\RoleNameEnum;
use Domain\Business\Models\Business;
use Domain\Business\Models\Member;
use Domain\Business\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
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
     * Seeds memberships for the 4 fixed users then fills businesses with extra users.
     *
     * Fixed scenarios (never change — used for predictable test assertions):
     *
     *   alice.owner  → Le Gourmet Parisien (owner)
     *                  Scénario: 1 seul business, propriétaire
     *
     *   bob.multi    → Le Gourmet Parisien (manager) + La Mie Dorée (owner)
     *                  Scénario: plusieurs businesses avec 2 rôles différents
     *
     *   charlie.free → aucune membership
     *                  Scénario: aucun business
     *
     *   diana.owner  → Traiteur des Délices (owner)
     *                  Scénario: propriétaire de la 2ème organisation
     *
     * Extra users are distributed across businesses as employees and viewers
     * to simulate a realistic production dataset.
     *
     * @return void
     */
    public function run(): void
    {
        $alice = User::query()->where('email', 'alice.owner@haccp.local')->firstOrFail();
        $bob   = User::query()->where('email', 'bob.multi@haccp.local')->firstOrFail();
        $diana = User::query()->where('email', 'diana.owner@haccp.local')->firstOrFail();

        $gourmet  = Business::query()->where('email', 'gourmet@haccp-solutions.fr')->firstOrFail();
        $miedoree = Business::query()->where('email', 'miedoree@haccp-solutions.fr')->firstOrFail();
        $traiteur = Business::query()->where('email', 'traiteur@delices-diana.fr')->firstOrFail();

        // ── Fixed memberships ──────────────────────────────────────────────────────

        // alice: 1 seul business, role owner
        $this->upsertMember(
            user:      $alice,
            business:  $gourmet,
            roleName:  RoleNameEnum::OWNER,
            status:    MemberStatusEnum::ACTIVE,
            joinedAt:  now()->subMonths(6),
            invitedBy: null,
        );

        // bob: Le Gourmet en tant que manager (rôle 1/2)
        $this->upsertMember(
            user:      $bob,
            business:  $gourmet,
            roleName:  RoleNameEnum::MANAGER,
            status:    MemberStatusEnum::ACTIVE,
            joinedAt:  now()->subMonths(3),
            invitedBy: $alice,
        );

        // bob: La Mie Dorée en tant que owner (rôle 2/2 — rôle différent du précédent)
        $this->upsertMember(
            user:      $bob,
            business:  $miedoree,
            roleName:  RoleNameEnum::OWNER,
            status:    MemberStatusEnum::ACTIVE,
            joinedAt:  now()->subMonths(4),
            invitedBy: null,
        );

        // charlie: aucune membership (scénario "aucun business") — intentionnellement absent

        // diana: Traiteur des Délices en tant que owner
        $this->upsertMember(
            user:      $diana,
            business:  $traiteur,
            roleName:  RoleNameEnum::OWNER,
            status:    MemberStatusEnum::ACTIVE,
            joinedAt:  now()->subMonths(2),
            invitedBy: null,
        );

        // ── Extra users: remplissage des businesses ────────────────────────────────

        /** @var Collection<int, User> $extraUsers */
        $extraUsers = User::query()
            ->whereNotIn('email', $this->fixedEmails)
            ->get()
            ->shuffle();

        $fillerSlots = [
            ['business' => $gourmet,  'roleName' => RoleNameEnum::EMPLOYEE],
            ['business' => $gourmet,  'roleName' => RoleNameEnum::VIEWER],
            ['business' => $miedoree, 'roleName' => RoleNameEnum::EMPLOYEE],
            ['business' => $miedoree, 'roleName' => RoleNameEnum::VIEWER],
            ['business' => $traiteur, 'roleName' => RoleNameEnum::EMPLOYEE],
            ['business' => $traiteur, 'roleName' => RoleNameEnum::VIEWER],
        ];

        foreach ($fillerSlots as $slotIndex => $slot) {
            $extraUser = $extraUsers->get($slotIndex);

            if (! $extraUser instanceof User) {
                continue;
            }

            $this->upsertMember(
                user:      $extraUser,
                business:  $slot['business'],
                roleName:  $slot['roleName'],
                status:    MemberStatusEnum::ACTIVE,
                joinedAt:  now()->subDays(random_int(7, 60)),
                invitedBy: null,
            );
        }
    }

    /**
     * Creates or updates a membership and syncs the role via the member_role pivot.
     * The role is resolved via the business relationship to guarantee it belongs to the right business.
     * Skips silently if the requested role does not exist for that business.
     *
     * @param  User              $user      The user to add as a member.
     * @param  Business          $business  The business the user belongs to.
     * @param  RoleNameEnum      $roleName  The role to assign (resolved by name within the business).
     * @param  MemberStatusEnum  $status    The membership status.
     * @param  Carbon            $joinedAt  When the user joined the business.
     * @param  User|null         $invitedBy The user who sent the invitation, or null if self-registered.
     * @return void
     */
    private function upsertMember(
        User $user,
        Business $business,
        RoleNameEnum $roleName,
        MemberStatusEnum $status,
        Carbon $joinedAt,
        ?User $invitedBy,
    ): void {
        /** @var Role|null $role */
        $role = $business->roles()
            ->where('name', $roleName->value)
            ->first();

        if (! $role instanceof Role) {
            return;
        }

        /** @var Member $member */
        $member = $business->members()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'invited_by' => $invitedBy?->id,
                'status'     => $status->value,
                'joined_at'  => $joinedAt,
                'invited_at' => $status === MemberStatusEnum::INVITED ? now() : null,
            ]
        );

        $member->roles()->sync([$role->id]);
    }
}
