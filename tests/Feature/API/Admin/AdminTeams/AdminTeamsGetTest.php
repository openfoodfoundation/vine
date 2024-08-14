<?php

namespace Tests\Feature\API\Admin\AdminTeams;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/teams';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanGetAllTeams()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $rand = rand(5, 10);

        Team::factory($rand)->create();

        $response    = $this->getJson($this->apiRoot . $this->endpoint);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($rand, $responseObj->data->total);
    }

    #[Test]
    public function itCanGetASingleTeam()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = Team::factory()->create();

        $response    = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($model->name, $responseObj->data->name);
    }
}
