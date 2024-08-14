<?php

namespace Tests\Feature\API\Admin\AdminTeamServiceTeams;

use App\Models\TeamServiceTeam;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamServiceTeamsDeleteTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/team-service-teams';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $model = TeamServiceTeam::factory()->create();

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanDeleteATeamServiceTeam()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = TeamServiceTeam::factory()->create();

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(200);
    }
}
