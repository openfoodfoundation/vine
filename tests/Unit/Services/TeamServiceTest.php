<?php

namespace Tests\Unit\Services;

use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function generateTeamInitialsItGeneratesInitials(): void
    {

        $team = Team::factory()->create(
            [
                'name' => 'this is a long team name',
            ]
        );

        $teamInitials = TeamService::generateTeamInitials($team);

        $this->assertEquals(expected: 'TIALTN', actual: $teamInitials);
    }

    #[Test]
    public function generateTeamInitialsItGeneratesInitialsRandom(): void
    {

        $team = Team::factory()->create(
            []
        );

        $teamInitials = TeamService::generateTeamInitials($team);

        $teamNameBits         = explode(' ', $team->name);
        $teamNameBitsInitials = '';

        foreach ($teamNameBits as $teamNameBit) {
            $teamNameBitsInitials .= substr($teamNameBit, 0, 1);
        }

        $teamNameBitsInitials = strtoupper($teamNameBitsInitials);

        $this->assertEquals(expected: $teamNameBitsInitials, actual: $teamInitials);
    }
}
