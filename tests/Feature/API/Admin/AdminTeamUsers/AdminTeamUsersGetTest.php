<?php

namespace Tests\Feature\API\Admin\AdminTeamUsers;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamUsersGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/team-users';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanGetAllTeamUsers()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $existing = TeamUser::count();
        $rand = rand(5, 10);
        $user = User::factory()->create();
        $team = Team::factory()->create();
        TeamUser::factory($rand)->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $response    = $this->getJson($this->apiRoot . $this->endpoint);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($existing + $rand, $responseObj->data->total);
    }

    #[Test]
    public function itCanNotGetASingleTeamUser()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $user  = User::factory()->create();
        $team  = Team::factory()->create();
        $model = TeamUser::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
