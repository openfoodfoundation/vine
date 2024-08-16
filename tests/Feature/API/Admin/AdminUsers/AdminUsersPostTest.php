<?php

namespace Tests\Feature\API\Admin\AdminUsers;

use App\Models\Team;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminUsersPostTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/users';

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
    public function itCanStoreAUser()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $team = Team::factory()->create();

        $payload = [
            'name'            => $this->faker->name(),
            'email'           => $this->faker->email(),
            'current_team_id' => $team->id,
        ];

        $response    = $this->postJson($this->apiRoot . $this->endpoint, $payload);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($payload['name'], $responseObj->data->name);
        $this->assertEquals($payload['email'], $responseObj->data->email);
        $this->assertEquals($payload['current_team_id'], $responseObj->data->current_team_id);
    }
}
