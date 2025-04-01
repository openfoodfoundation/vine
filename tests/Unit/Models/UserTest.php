<?php

namespace Tests\Unit\Models;

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
}
