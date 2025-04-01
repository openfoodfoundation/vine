<?php

namespace Tests\Feature\API\Admin\AdminTeamServiceTeams;

use App\Models\TeamServiceTeam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamServiceTeamsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/team-service-teams';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanGetAllTeamServiceTeams()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $existing = TeamServiceTeam::count();

        $rand = rand(5, 10);

        TeamServiceTeam::factory($rand)->create();

        $response    = $this->getJson($this->apiRoot . $this->endpoint);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($existing + $rand, $responseObj->data->total);
    }

    #[Test]
    public function itCanNotGetASingleTeamServiceTeam()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = TeamServiceTeam::factory()->create();

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
