<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\TeamServiceTeam;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamServiceTeamTest extends TestCase
{
    #[Test]
    public function its_has_a_team(): void
    {
        $team            = Team::factory()->create();
        $teamServiceTeam = TeamServiceTeam::factory()->create([
            'team_id' => $team->id,
        ]);

        $teamServiceTeamWithTeam = TeamServiceTeam::with('team')->find($teamServiceTeam->id);

        $this->assertInstanceOf(TeamServiceTeam::class, $teamServiceTeamWithTeam);
        $this->assertInstanceOf(Team::class, $teamServiceTeamWithTeam->team);
        $this->assertSame($team->id, $teamServiceTeamWithTeam->team->id);
    }

    #[Test]
    public function it_has_a_service_team(): void
    {
        $serviceTeam     = Team::factory()->create();
        $teamServiceTeam = TeamServiceTeam::factory()->create([
            'service_team_id' => $serviceTeam->id,
        ]);

        $teamServiceTeamWithServiceTeam = TeamServiceTeam::with('team')->find($teamServiceTeam->id);

        $this->assertInstanceOf(TeamServiceTeam::class, $teamServiceTeamWithServiceTeam);
        $this->assertInstanceOf(Team::class, $teamServiceTeamWithServiceTeam->serviceTeam);
        $this->assertSame($serviceTeam->id, $teamServiceTeamWithServiceTeam->serviceTeam->id);
    }
}
