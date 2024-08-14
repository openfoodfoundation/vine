<?php

namespace Tests\Feature\API\Admin\AdminTeams;

use App\Models\Team;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamsPutTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/teams';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $model = Team::factory()->create();

        $payload = [];

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanUpdateATeam()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = Team::factory()->create();

        $payload = [
            'name' => $this->faker->name(),
        ];

        $response    = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($payload['name'], $responseObj->data->name);
    }
}
