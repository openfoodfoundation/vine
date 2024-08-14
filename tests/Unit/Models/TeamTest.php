<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamTest extends TestCase
{
    #[Test]
    public function testTeamUserRelation(): void
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
    public function testUserRelation(): void
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
}
