<?php

namespace Tests\Feature\API\Admin\AdminTeamMerchantTeams;

use App\Enums\ApiResponse;
use App\Models\Country;
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
    public function itCanStoreATeamMerchantTeamIfSameCountry()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $randomCountryId = rand(0, (Country::count() - 1));

        $team = Team::factory()->create([
            'country_id' => $randomCountryId,
        ]);
        $merchantTeam = Team::factory()->create([
            'country_id' => $randomCountryId,
        ]);

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

    #[Test]
    public function itCanNotStoreATeamMerchantTeamIfNotSameCountry()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $randomCountryId1 = rand(0, 150);
        $randomCountryId2 = rand(151, (Country::count() - 1));

        $team = Team::factory()->create([
            'country_id' => $randomCountryId1,
        ]);
        $merchantTeam = Team::factory()->create([
            'country_id' => $randomCountryId2,
        ]);

        $payload = [
            'team_id'          => $team->id,
            'merchant_team_id' => $merchantTeam->id,
        ];

        $response    = $this->postJson($this->apiRoot . $this->endpoint, $payload);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(404);
        $this->assertEquals($responseObj->meta->message, ApiResponse::RESPONSE_COUNTRY_MISMATCH->value);
    }
}
