<?php

namespace Tests\Feature\API\Admin\AdminTeamUsers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamUsersPostTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/team-users';

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
    public function itCanStoreATeamUser()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $user = User::factory()->create();
        $team = Team::factory()->create();

        $payload = [
            'user_id' => $user->id,
            'team_id' => $team->id,
        ];

        $response    = $this->postJson($this->apiRoot . $this->endpoint, $payload);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($payload['user_id'], $responseObj->data->user_id, $user->id);
        $this->assertEquals($payload['team_id'], $responseObj->data->team_id, $team->id);
    }
}
