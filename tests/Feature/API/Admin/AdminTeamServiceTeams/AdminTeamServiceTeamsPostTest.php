<?php

namespace Tests\Feature\API\Admin\AdminTeamServiceTeams;

use App\Models\Team;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamServiceTeamsPostTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/team-service-teams';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $payload = [];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanStoreATeamServiceTeam()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $team        = Team::factory()->create();
        $serviceTeam = Team::factory()->create();

        $payload = [
            'team_id'         => $team->id,
            'service_team_id' => $serviceTeam->id,
        ];

        $response    = $this->postJson($this->apiRoot . $this->endpoint, $payload);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($payload['team_id'], $responseObj->data->team_id);
        $this->assertEquals($payload['service_team_id'], $responseObj->data->service_team_id);
    }
}
