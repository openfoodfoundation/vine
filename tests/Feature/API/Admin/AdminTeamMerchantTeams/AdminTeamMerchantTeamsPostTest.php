<?php

namespace Tests\Feature\API\Admin\AdminTeamMerchantTeams;

use App\Models\Team;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamMerchantTeamsPostTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/team-merchant-teams';

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
    public function itCanStoreATeamMerchantTeam()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $team         = Team::factory()->create();
        $merchantTeam = Team::factory()->create();

        $payload = [
            'team_id'          => $team->id,
            'merchant_team_id' => $merchantTeam->id,
        ];

        $response    = $this->postJson($this->apiRoot . $this->endpoint, $payload);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($payload['team_id'], $responseObj->data->team_id);
        $this->assertEquals($payload['merchant_team_id'], $responseObj->data->merchant_team_id);
    }
}
