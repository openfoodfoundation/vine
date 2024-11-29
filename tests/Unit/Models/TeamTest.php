<?php

namespace Tests\Unit\Models;

use App\Models\AuditItem;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamTest extends TestCase
{
    #[Test]
    public function test_team_user_relation(): void
    {
        $team = Team::factory()->create();

        $users = User::factory(5)->create();

        $teamUsers = collect();

        $users->map(function ($user) use ($team, $teamUsers) {
            $teamUsers->add(
                TeamUser::factory()->create([
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                ])
            );
        });

        $teamWithTeamUsers = Team::with('teamUsers')->find($team->id);

        $this->assertInstanceOf(Team::class, $teamWithTeamUsers);
        $this->assertContainsOnlyInstancesOf(TeamUser::class, $teamWithTeamUsers->teamUsers);

        $teamUsers->map(function ($teamUser) use ($teamWithTeamUsers) {
            $this->assertTrue($teamWithTeamUsers->teamUsers->contains($teamUser));
        });
    }

    #[Test]
    public function test_user_relation(): void
    {
        $team = Team::factory()->create();

        $users = User::factory(5)->create();

        $teamUsers = collect();

        $users->map(function ($user) use ($team, $teamUsers) {
            $teamUsers->add(
                TeamUser::factory()->create([
                    'user_id' => $user->id,
                    'team_id' => $team->id,
                ])
            );
        });

        $teamWithUsers = Team::with('teamUsers.user')->find($team->id);

        $this->assertInstanceOf(Team::class, $teamWithUsers);
        $teamWithUsers->teamUsers->map(function ($teamUser) use ($users) {
            $this->assertInstanceOf(User::class, $teamUser->user);
            $this->assertTrue($users->contains($teamUser->user));
        });
    }

    #[Test]
    public function test_audit_items_relation(): void
    {
        $team = Team::factory()->create();

        $num = rand(1, 10);

        AuditItem::factory($num)->create([
            'auditable_type' => Team::class,
            'auditable_id'   => $team->id,
        ]);

        // Same ID but different class, should NOT be returned
        AuditItem::factory($num)->create([
            'auditable_type' => User::class,
            'auditable_id'   => $team->id,
        ]);

        // Same class but different ID, should NOT be returned
        AuditItem::factory($num)->create([
            'auditable_type' => Team::class,
            'auditable_id'   => $team->id + 1,
        ]);

        $teamWithAuditItems = Team::with('auditItems')->find($team->id);

        $this->assertInstanceOf(Team::class, $teamWithAuditItems);

        $teamWithAuditItems->auditItems->map(function ($auditItem) use ($team) {
            $this->assertEquals($team->id, $auditItem->auditable_id);
            $this->assertSame(Team::class, $auditItem->auditable_type);
            $this->assertInstanceOf(AuditItem::class, $auditItem);
        });
    }
}
