<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\TeamMerchantTeam;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamMerchantTeamTest extends TestCase
{
    #[Test]
    public function it_has_a_team(): void
    {
        $team             = Team::factory()->create();
        $teamMerchantTeam = TeamMerchantTeam::factory()->create([
            'team_id' => $team->id,
        ]);

        $teamMerchantTeamWithTeam = TeamMerchantTeam::with('team')->find($teamMerchantTeam->id);

        $this->assertInstanceOf(TeamMerchantTeam::class, $teamMerchantTeamWithTeam);
        $this->assertInstanceOf(Team::class, $teamMerchantTeamWithTeam->team);
        $this->assertSame($team->id, $teamMerchantTeamWithTeam->team->id);
    }

    #[Test]
    public function it_has_a_team_merchant_team(): void
    {
        $merchantTeam     = Team::factory()->create();
        $teamMerchantTeam = TeamMerchantTeam::factory()->create([
            'merchant_team_id' => $merchantTeam->id,
        ]);

        $teamMerchantTeamWithMerchantTeam = TeamMerchantTeam::with('merchantTeam')
            ->find($teamMerchantTeam->id);

        $this->assertInstanceOf(Team::class, $teamMerchantTeamWithMerchantTeam->merchantTeam);
        $this->assertSame($merchantTeam->id, $teamMerchantTeamWithMerchantTeam->merchantTeam->id);
    }
}
