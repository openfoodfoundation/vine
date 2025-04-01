<?php

namespace Tests\Feature\API\Admin\AdminTeamMerchantTeams;

use App\Models\TeamMerchantTeam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamMerchantTeamsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/team-merchant-teams';

    #[Test]
    public function only_admin_can_access(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_get_all_team_merchant_teams()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $existing = TeamMerchantTeam::count();

        $rand = rand(5, 10);

        TeamMerchantTeam::factory($rand)->create();

        $response    = $this->getJson($this->apiRoot . $this->endpoint);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);

        $this->assertEquals($existing + $rand, $responseObj->data->total);
    }

    #[Test]
    public function it_can_not_get_a_single_team_merchant_team()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = TeamMerchantTeam::factory()->create();

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
