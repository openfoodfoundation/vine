<?php

namespace Tests\Unit\Models;

use App\Models\AuditItem;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    #[Test]
    public function testTeamUserRelation(): void
    {
        $user = User::factory()->create();

        $team = Team::factory()->create();

        $teamUser = TeamUser::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $userWithTeamUsers = User::with('teamUsers')->find($user->id);

        $this->assertInstanceOf(User::class, $userWithTeamUsers);
        $this->assertInstanceOf(TeamUser::class, $userWithTeamUsers->teamUsers->first());
        $this->assertSame($teamUser->user_id, $userWithTeamUsers->teamUsers->first()->user_id);
        $this->assertSame($teamUser->team_id, $userWithTeamUsers->teamUsers->first()->team_id);
    }

    #[Test]
    public function testAuditItemsRelation(): void
    {
        $user = User::factory()->create();

        $num = rand(1, 10);

        AuditItem::factory($num)->create([
            'auditable_type' => User::class,
            'auditable_id' => $user->id,
        ]);

        // Same ID but different class, should NOT be returned
        AuditItem::factory($num)->create([
            'auditable_type' => Team::class,
            'auditable_id' => $user->id,
        ]);

        // Same class but different ID, should NOT be returned
        AuditItem::factory($num)->create([
            'auditable_type' => User::class,
            'auditable_id' => $user->id + 1,
        ]);

        $userWithAuditItems = User::with('auditItems')->find($user->id);

        $this->assertInstanceOf(User::class, $userWithAuditItems);
        $this->assertCount($num, $userWithAuditItems->auditItems);
        $userWithAuditItems->auditItems->map(function ($auditItem) use ($user) {
            $this->assertEquals($user->id, $auditItem->auditable_id);
            $this->assertSame(User::class, $auditItem->auditable_type);
            $this->assertInstanceOf(AuditItem::class, $auditItem);
        });
    }
}
